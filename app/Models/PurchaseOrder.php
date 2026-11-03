<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    public function recalculateSubtotal(): void
    {
        $this->updateQuietly([
            'subtotal' => $this->items()->sum('line_total'),
        ]);
    }
}
