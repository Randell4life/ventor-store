<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Order extends Model
{
    protected static function booted(): void
    {
        static::updating(function (Order $order): void {
            if (! $order->isDirty('status')) {
                return;
            }

            $previousStatus = $order->getOriginal('status');

            if ($previousStatus === 'cancelled') {
                throw ValidationException::withMessages([
                    'status' => 'გაუქმებული შეკვეთის სტატუსის შეცვლა აღარ შეიძლება.',
                ]);
            }

            if ($order->status === 'cancelled' && ! in_array($previousStatus, ['new', 'confirmed', 'processing'], true)) {
                throw ValidationException::withMessages([
                    'status' => 'შეკვეთის გაუქმება შესაძლებელია მხოლოდ გაგზავნამდე.',
                ]);
            }
        });

        static::updated(function (Order $order): void {
            if (! $order->wasChanged('status')) {
                return;
            }

            if ($order->status === 'cancelled') {
                DB::transaction(function () use ($order): void {
                    $lockedOrder = static::query()->whereKey($order->getKey())->lockForUpdate()->first();

                    if (! $lockedOrder || $lockedOrder->status !== 'cancelled') {
                        return;
                    }

                    $reference = 'ORDER-CANCEL-'.$lockedOrder->id;

                    if (! InventoryMovement::query()->where('reference', $reference)->exists()) {
                        foreach ($lockedOrder->items()->whereNotNull('product_id')->get() as $item) {
                            $product = Product::query()->whereKey($item->product_id)->lockForUpdate()->first();

                            if (! $product) {
                                continue;
                            }

                            $before = (int) $product->stock;
                            $quantity = (int) $item->quantity;
                            $after = $before + $quantity;

                            $product->update(['stock' => $after]);

                            InventoryMovement::create([
                                'product_id' => $product->id,
                                'user_id' => auth()->id(),
                                'movement_type' => 'sale_return',
                                'quantity_change' => $quantity,
                                'stock_before' => $before,
                                'stock_after' => $after,
                                'reference' => $reference,
                                'note' => 'შეკვეთის გაუქმება: '.$lockedOrder->order_number,
                            ]);
                        }
                    }

                    if ($lockedOrder->coupon_id) {
                        $coupon = Coupon::query()->whereKey($lockedOrder->coupon_id)->lockForUpdate()->first();

                        if ($coupon && $coupon->used_count > 0) {
                            $coupon->decrement('used_count');
                        }
                    }

                    $redeemedPoints = (int) $lockedOrder->loyalty_points_redeemed;

                    if ($lockedOrder->user_id && $redeemedPoints > 0) {
                        $user = User::query()->whereKey($lockedOrder->user_id)->lockForUpdate()->first();

                        if ($user && ! $user->loyaltyPointTransactions()
                            ->where('order_id', $lockedOrder->id)
                            ->where('type', 'refunded')
                            ->exists()) {
                            $user->increment('loyalty_points', $redeemedPoints);
                            $user->loyaltyPointTransactions()->create([
                                'order_id' => $lockedOrder->id,
                                'type' => 'refunded',
                                'points' => $redeemedPoints,
                                'balance_after' => (int) $user->fresh()->loyalty_points,
                                'description' => 'დაბრუნებული ქულები გაუქმებული შეკვეთიდან '.$lockedOrder->order_number,
                            ]);
                        }
                    }
                });

                return;
            }

            if ($order->status !== 'completed') {
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
