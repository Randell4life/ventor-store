<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected static function booted(): void
    {
        static::updated(function (Order $order): void {
            if (! $order->wasChanged('status') || $order->status !== 'completed') {
                return;
            }

            DB::transaction(function () use ($order): void {
                $lockedOrder = static::query()->whereKey($order->getKey())->lockForUpdate()->first();

                if (! $lockedOrder || $lockedOrder->status !== 'completed' || $lockedOrder->loyalty_points_awarded_at) {
                    return;
                }

                if (! $lockedOrder->user_id) {
                    $lockedOrder->forceFill([
                        'loyalty_points_earned' => 0,
                        'loyalty_points_awarded_at' => now(),
                    ])->saveQuietly();

                    return;
                }

                $user = User::query()->whereKey($lockedOrder->user_id)->lockForUpdate()->first();

                if (! $user) {
                    return;
                }

                $points = max(0, (int) floor((float) $lockedOrder->total));

                if ($points > 0) {
                    $user->increment('loyalty_points', $points);
                    $user->loyaltyPointTransactions()->create([
                        'order_id' => $lockedOrder->id,
                        'type' => 'earned',
                        'points' => $points,
                        'balance_after' => (int) $user->fresh()->loyalty_points,
                        'description' => 'ქულები შეკვეთის დასრულებისთვის '.$lockedOrder->order_number,
                    ]);
                }

                $lockedOrder->forceFill([
                    'loyalty_points_earned' => $points,
                    'loyalty_points_awarded_at' => now(),
                ])->saveQuietly();
            });
        });
    }

    protected $fillable = [
        'user_id',
        'order_number',
        'public_token',
        'customer_name',
        'phone',
        'email',
        'city',
        'address',
        'note',
        'subtotal',
        'delivery_fee',
        'total',
        'coupon_id',
        'coupon_code',
        'discount_amount',
        'loyalty_points_earned',
        'loyalty_points_redeemed',
        'loyalty_points_awarded_at',
        'status',
        'payment_method',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'loyalty_points_earned' => 'integer',
            'loyalty_points_redeemed' => 'integer',
            'loyalty_points_awarded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
