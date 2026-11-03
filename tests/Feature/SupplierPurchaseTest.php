<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_supplier_can_have_purchase_order_with_calculated_totals(): void
    {
        $supplier = Supplier::create([
            'name' => 'VENTOR Test Supplier',
            'phone' => '555123456',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Test Car Shampoo',
            'slug' => 'test-car-shampoo',
            'sku' => 'VT-SHAMPOO-01',
            'price' => 30,
            'stock' => 4,
            'is_active' => true,
        ]);

        $order = PurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'status' => 'ordered',
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 10,
            'unit_cost' => 8.75,
            'line_total' => 0,
        ]);

        $this->assertNotEmpty($order->fresh()->order_number);
        $this->assertSame('87.50', $order->fresh()->subtotal);
        $this->assertDatabaseHas('purchase_order_items', [
            'purchase_order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Test Car Shampoo',
            'quantity' => 10,
            'line_total' => 87.50,
        ]);
    }

    public function test_purchase_item_keeps_product_name_snapshot_when_product_is_deleted(): void
    {
        $supplier = Supplier::create(['name' => 'Supplier A']);
        $product = Product::create([
            'name' => 'Archived Product',
            'slug' => 'archived-product',
            'price' => 10,
            'stock' => 0,
            'is_active' => true,
        ]);

        $order = PurchaseOrder::create(['supplier_id' => $supplier->id]);
        $item = $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 3,
            'unit_cost' => 4,
            'line_total' => 12,
        ]);

        $product->delete();

        $this->assertDatabaseHas('purchase_order_items', [
            'id' => $item->id,
            'product_id' => null,
            'product_name' => 'Archived Product',
        ]);
    }
}
