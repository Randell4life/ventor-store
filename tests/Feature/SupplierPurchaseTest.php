<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
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

    public function test_supplier_purchase_order_supports_partial_payments_and_tracks_balance(): void
    {
        $supplier = Supplier::create(['name' => 'Payment Supplier']);
        $order = PurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'status' => 'received',
            'subtotal' => 250,
        ]);

        $firstPayment = $order->recordPayment(100, 'bank_transfer', 'TRX-001');
        $secondPayment = $order->recordPayment(50, 'cash');

        $this->assertSame('100.00', $firstPayment->amount);
        $this->assertSame('50.00', $secondPayment->amount);
        $this->assertEquals(150.0, $order->fresh()->paidAmount());
        $this->assertEquals(100.0, $order->fresh()->balanceDue());
        $this->assertSame('partial', $order->fresh()->paymentStatus());
        $this->assertEquals(100.0, $supplier->fresh()->outstandingBalance());
    }

    public function test_received_purchase_order_items_cannot_be_changed_or_deleted(): void
    {
        $supplier = Supplier::create(['name' => 'Locked Supplier']);
        $product = Product::create([
            'name' => 'Locked Product',
            'slug' => 'locked-product',
            'sku' => 'LOCK-001',
            'price' => 20,
            'stock' => 0,
            'is_active' => true,
        ]);

        $order = PurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'status' => 'ordered',
        ]);

        $item = $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 5,
            'unit_cost' => 4,
            'line_total' => 20,
        ]);

        $order->update(['status' => 'received']);

        try {
            $item->update(['quantity' => 10]);
            $this->fail('Received purchase order items must not be editable.');
        } catch (ValidationException) {
            $this->assertSame(5, (int) $item->fresh()->quantity);
        }

        try {
            $item->delete();
            $this->fail('Received purchase order items must not be deletable.');
        } catch (ValidationException) {
            $this->assertDatabaseHas('purchase_order_items', [
                'id' => $item->id,
                'purchase_order_id' => $order->id,
                'quantity' => 5,
            ]);
        }
    }

    public function test_supplier_payment_cannot_exceed_remaining_balance_or_be_recorded_before_receipt(): void
    {
        $supplier = Supplier::create(['name' => 'Protected Supplier']);
        $draftOrder = PurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'status' => 'ordered',
            'subtotal' => 100,
        ]);

        try {
            $draftOrder->recordPayment(10);
            $this->fail('Payment should not be accepted before goods are received.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('supplier_payments', 0);
        }

        $receivedOrder = PurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'status' => 'received',
            'subtotal' => 100,
        ]);

        $receivedOrder->recordPayment(80);

        $this->expectException(ValidationException::class);
        $receivedOrder->recordPayment(20.01);
    }
}
