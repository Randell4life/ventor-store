<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'minimum_order', 'maximum_discount', 'usage_limit', 'starts_at', 'ends_at', 'is_active'];

    protected function casts(): array
    {
        return ['value' => 'decimal:2', 'minimum_order' => 'decimal:2', 'maximum_discount' => 'decimal:2', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'is_active' => 'boolean'];
    }

    public function isUsableFor(float $subtotal): bool
    {
        $now = now();
        return $this->is_active
            && (float) $this->value > 0
            && ($this->type !== 'percent' || (float) $this->value <= 100)
            && $subtotal >= (float) $this->minimum_order
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit)
            && ($this->starts_at === null || $this->starts_at->lte($now))
            && ($this->ends_at === null || $this->ends_at->gte($now));
    }

    public function calculateDiscount(float $subtotal): float
    {
        $discount = $this->type === 'fixed' ? (float) $this->value : $subtotal * ((float) $this->value / 100);
        if ($this->maximum_discount !== null) $discount = min($discount, (float) $this->maximum_discount);
        return round(min($subtotal, max(0, $discount)), 2);
    }
}
