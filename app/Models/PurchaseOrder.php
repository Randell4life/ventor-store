<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'supplier_id', 'created_by', 'order_number', 'status',
        'subtotal', 'notes', 'received_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'received_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PurchaseOrder $order): void {
            $order->order_number = $order->order_number ?: 'PO-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
            $order->created_by = $order->created_by ?: auth()->id();
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function paidAmount(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function balanceDue(): float
    {
        return max(0, round((float) $this->subtotal - $this->paidAmount(), 2));
    }

    public function paymentStatus(): string
    {
        if ($this->paidAmount() <= 0) {
            return 'unpaid';
        }

        return $this->balanceDue() > 0 ? 'partial' : 'paid';
    }

    public function recordPayment(
        float $amount,
        string $paymentMethod = 'bank_transfer',
        ?string $reference = null,
        ?string $note = null,
    ): SupplierPayment {
        return DB::transaction(function () use ($amount, $paymentMethod, $reference, $note): SupplierPayment {
            $order = static::query()->lockForUpdate()->findOrFail($this->getKey());
            $amountInCents = (int) round($amount * 100);
            $paidInCents = (int) round((float) $order->payments()->sum('amount') * 100);
            $totalInCents = (int) round((float) $order->subtotal * 100);
            $remainingInCents = max(0, $totalInCents - $paidInCents);

            if ($order->status !== 'received') {
                throw ValidationException::withMessages([
                    'amount' => 'გადახდის დაფიქსირება შესაძლებელია მხოლოდ მიღებულ შესყიდვაზე.',
                ]);
            }

            if (! is_finite($amount) || $amountInCents <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'გადახდის თანხა უნდა იყოს 0-ზე მეტი.',
                ]);
            }

            if ($amountInCents > $remainingInCents) {
                throw ValidationException::withMessages([
                    'amount' => 'გადახდის თანხა დარჩენილ დავალიანებას აღემატება.',
                ]);
            }

            return $order->payments()->create([
                'supplier_id' => $order->supplier_id,
                'user_id' => auth()->id(),
                'amount' => number_format($amountInCents / 100, 2, '.', ''),
                'payment_method' => $paymentMethod,
                'reference' => $reference,
                'note' => $note,
                'paid_at' => now(),
            ]);
        });
    }

    public function recalculateSubtotal(): void
    {
        $this->updateQuietly([
            'subtotal' => $this->items()->sum('line_total'),
        ]);
    }
}
