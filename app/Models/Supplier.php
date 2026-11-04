<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'contact_person', 'phone', 'email', 'tax_number',
        'address', 'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function outstandingBalance(): float
    {
        return (float) $this->purchaseOrders()
            ->where('status', 'received')
            ->withSum('payments', 'amount')
            ->get()
            ->sum(fn (PurchaseOrder $order): float => max(
                0,
                (float) $order->subtotal - (float) ($order->payments_sum_amount ?? 0)
            ));
    }
}
