<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id', 'product_id', 'product_name', 'sku',
        'quantity', 'unit_cost', 'line_total',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_cost' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (PurchaseOrderItem $item): void {
            $purchaseOrderStatus = PurchaseOrder::query()
                ->whereKey($item->purchase_order_id)
                ->value('status');

            if (! in_array($purchaseOrderStatus, ['draft', 'ordered'], true)) {
                throw ValidationException::withMessages([
                    'items' => 'მიღებული ან გაუქმებული შესყიდვის პოზიციების შეცვლა შეუძლებელია.',
                ]);
            }

            if ($item->product_id) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $item->product_name = $product->name;
                    $item->sku = $product->sku;
                }
            }

            $item->line_total = (int) $item->quantity * (float) $item->unit_cost;
        });

        static::saved(function (PurchaseOrderItem $item): void {
            $item->purchaseOrder?->recalculateSubtotal();
        });

        static::deleting(function (PurchaseOrderItem $item): void {
            $purchaseOrderStatus = PurchaseOrder::query()
                ->whereKey($item->purchase_order_id)
                ->value('status');

            if (! in_array($purchaseOrderStatus, ['draft', 'ordered'], true)) {
                throw ValidationException::withMessages([
                    'items' => 'მიღებული ან გაუქმებული შესყიდვის პოზიციების წაშლა შეუძლებელია.',
                ]);
            }
        });

        static::deleted(function (PurchaseOrderItem $item): void {
            $item->purchaseOrder?->recalculateSubtotal();
        });
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
