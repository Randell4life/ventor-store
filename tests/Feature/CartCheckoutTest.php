<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\LoyaltyPointTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function product(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'name' => 'VENTOR Test Cleaner',
            'slug' => 'ventor-test-cleaner',
            'sku' => 'VT-TEST-01',
            'price' => 25.50,
            'stock' => 5,
            'is_active' => true,
        ], $overrides));
    }

    public function test_customer_can_add_product_and_cart_shows_summary(): void
    {
        $product = $this->product();

        $this->post('/cart', [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertRedirect('/cart');

        $this->get('/cart')
            ->assertOk()
            ->assertSee('VENTOR Test Cleaner')
            ->assertSee('51.00')
            ->assertSee('შეკვეთის შეჯამება');
    }

    public function test_cart_rejects_quantity_above_available_stock(): void
    {
        $product = $this->product(['stock' => 2]);

        $this->from('/product/'.$product->slug)->post('/cart', [
            'product_id' => $product->id,
            'quantity' => 3,
        ])->assertSessionHasErrors('cart');

        $this->assertSame([], session('cart', []));
    }

    public function test_checkout_rechecks_stock_and_deducts_inventory_on_success(): void
    {
        $product = $this->product(['stock' => 5]);
        $this->withSession(['cart' => [(string) $product->id => 2]]);

        $this->post('/checkout', [
            'customer_name' => 'Test Buyer',
            'phone' => '555123456',
            'email' => 'buyer@example.com',
            'city' => 'Tbilisi',
            'address' => 'Test Street 1',
            'payment_method' => 'cash_on_delivery',
        ])->assertRedirect();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3,
        ]);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'movement_type' => 'sale',
            'quantity_change' => -2,
            'stock_before' => 5,
            'stock_after' => 3,
        ]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Test Buyer',
            'subtotal' => 51.00,
            'total' => 51.00,
        ]);

        $this->assertSame([], session('cart', []));
    }

    public function test_checkout_snapshots_product_cost_on_order_item(): void
    {
        $product = $this->product(['cost_price' => 8.25, 'price' => 25.50, 'stock' => 5]);
        $this->withSession(['cart' => [(string) $product->id => 2]]);

        $this->post('/checkout', [
            'customer_name' => 'Cost Snapshot Buyer',
            'phone' => '555123456',
            'city' => 'Tbilisi',
            'address' => 'Test Street 1',
            'payment_method' => 'cash_on_delivery',
        ])->assertRedirect();

        $order = Order::with('items')->firstOrFail();
        $this->assertSame('8.25', $order->items->first()->unit_cost);

        $product->update(['cost_price' => 12.50]);

        $this->assertSame('8.25', $order->items->first()->fresh()->unit_cost);
        $this->assertSame(16.50, (float) $order->items->sum(fn ($item) => (float) $item->unit_cost * $item->quantity));
    }


    public function test_cancelling_eligible_order_restores_stock_and_redeemed_points(): void
    {
        $user = User::create([
            'name' => 'Cancel Buyer',
            'email' => 'cancel-buyer@example.com',
            'password' => 'StrongPassword123!',
            'is_admin' => false,
            'loyalty_points' => 400,
        ]);
        $product = $this->product(['stock' => 3]);
        $coupon = Coupon::create([
            'code' => 'CANCEL100',
            'type' => 'fixed',
            'value' => 10,
            'minimum_order' => 0,
            'used_count' => 1,
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'VNT-TEST-CANCEL-01',
            'customer_name' => 'Cancel Buyer',
            'phone' => '555123456',
            'city' => 'Tbilisi',
            'address' => 'Test Street 1',
            'subtotal' => 51,
            'delivery_fee' => 0,
            'total' => 41,
            'loyalty_points_redeemed' => 100,
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'status' => 'processing',
            'payment_method' => 'cash_on_delivery',
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 2,
            'unit_price' => 25.50,
            'unit_cost' => 8.25,
            'line_total' => 51,
        ]);

        \\App\\Models\\InventoryMovement::create([
            'product_id' => $product->id,
            'movement_type' => 'sale',
            'quantity_change' => -2,
            'stock_before' => 5,
            'stock_after' => 3,
            'reference' => $order->order_number,
            'note' => 'ტესტის საწყობის ჩამოწერა',
        ]);

        $order->update(['status' => 'cancelled']);

        $this->assertSame(5, (int) $product->fresh()->stock);
        $this->assertSame(500, (int) $user->fresh()->loyalty_points);
        $this->assertSame(0, (int) $coupon->fresh()->used_count);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'movement_type' => 'sale_return',
            'quantity_change' => 2,
            'stock_before' => 3,
            'stock_after' => 5,
            'reference' => 'ORDER-CANCEL-'.$order->id,
        ]);
        $this->assertDatabaseHas('loyalty_point_transactions', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'type' => 'refunded',
            'points' => 100,
            'balance_after' => 500,
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $order->update(['status' => 'processing']);
    }


    public function test_cancelling_legacy_order_without_sale_movement_does_not_increase_stock(): void
    {
        $product = $this->product(['stock' => 3]);

        $order = Order::create([
            'order_number' => 'VNT-LEGACY-CANCEL-01',
            'customer_name' => 'Legacy Buyer',
            'phone' => '555123456',
            'city' => 'Tbilisi',
            'address' => 'Test Street 1',
            'subtotal' => 51,
            'delivery_fee' => 0,
            'total' => 51,
            'status' => 'processing',
            'payment_method' => 'cash_on_delivery',
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 2,
            'unit_price' => 25.50,
            'unit_cost' => 8.25,
            'line_total' => 51,
        ]);

        $order->update(['status' => 'cancelled']);

        $this->assertSame(3, (int) $product->fresh()->stock);
        $this->assertDatabaseMissing('inventory_movements', [
            'product_id' => $product->id,
            'movement_type' => 'sale_return',
            'reference' => 'ORDER-CANCEL-'.$order->id,
        ]);
    }

    public function test_shipped_order_cannot_be_cancelled(): void
    {
        $order = Order::create([
            'order_number' => 'VNT-TEST-SHIPPED-01',
            'customer_name' => 'Shipped Buyer',
            'phone' => '555123456',
            'city' => 'Tbilisi',
            'address' => 'Test Street 1',
            'subtotal' => 10,
            'delivery_fee' => 0,
            'total' => 10,
            'status' => 'shipped',
            'payment_method' => 'cash_on_delivery',
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $order->update(['status' => 'cancelled']);
    }

    public function test_completed_order_awards_points_only_once(): void
    {
        $user = User::create([
            'name' => 'Completed Buyer',
            'email' => 'completed-buyer@example.com',
            'password' => 'StrongPassword123!',
            'is_admin' => false,
            'loyalty_points' => 0,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'VNT-TEST-REWARD-01',
            'customer_name' => 'Completed Buyer',
            'phone' => '555123456',
            'city' => 'Tbilisi',
            'address' => 'Test Street 1',
            'subtotal' => 125.75,
            'delivery_fee' => 0,
            'total' => 125.75,
            'status' => 'new',
            'payment_method' => 'cash_on_delivery',
        ]);

        $order->update(['status' => 'completed']);
        $order->update(['status' => 'processing']);
        $order->update(['status' => 'completed']);

        $this->assertSame(125, (int) $user->fresh()->loyalty_points);
        $this->assertDatabaseCount('loyalty_point_transactions', 1);
        $this->assertDatabaseHas('loyalty_point_transactions', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'type' => 'earned',
            'points' => 125,
            'balance_after' => 125,
        ]);
        $this->assertSame(125, (int) $order->fresh()->loyalty_points_earned);
        $this->assertNotNull($order->fresh()->loyalty_points_awarded_at);
    }

    public function test_guest_order_does_not_award_loyalty_points(): void
    {
        $order = Order::create([
            'order_number' => 'VNT-TEST-GUEST-01',
            'customer_name' => 'Guest Buyer',
            'phone' => '555123456',
            'city' => 'Tbilisi',
            'address' => 'Test Street 1',
            'subtotal' => 50,
            'delivery_fee' => 0,
            'total' => 50,
            'status' => 'new',
            'payment_method' => 'cash_on_delivery',
        ]);

        $order->update(['status' => 'completed']);

        $this->assertSame(0, (int) $order->fresh()->loyalty_points_earned);
        $this->assertNotNull($order->fresh()->loyalty_points_awarded_at);
        $this->assertDatabaseCount('loyalty_point_transactions', 0);
    }

    public function test_customer_can_redeem_points_and_transaction_is_linked_to_order(): void
    {
        $user = User::create([
            'name' => 'Rewards Buyer',
            'email' => 'rewards-buyer@example.com',
            'password' => 'StrongPassword123!',
            'is_admin' => false,
            'loyalty_points' => 500,
        ]);
        $product = $this->product(['price' => 25.50, 'stock' => 5]);

        $this->actingAs($user)->withSession([
            'cart' => [(string) $product->id => 2],
        ])->post('/checkout', [
            'customer_name' => 'Rewards Buyer',
            'phone' => '555123456',
            'email' => 'rewards-buyer@example.com',
            'city' => 'Tbilisi',
            'address' => 'Test Street 1',
            'payment_method' => 'cash_on_delivery',
            'loyalty_points_to_redeem' => 100,
        ])->assertRedirect();

        $order = Order::firstOrFail();

        $this->assertSame('50.00', $order->total);
        $this->assertSame(100, $order->loyalty_points_redeemed);
        $this->assertSame(400, (int) $user->fresh()->loyalty_points);
        $this->assertDatabaseHas('loyalty_point_transactions', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'type' => 'redeemed',
            'points' => 100,
            'balance_after' => 400,
        ]);
    }

    public function test_customer_cannot_redeem_more_points_than_their_balance(): void
    {
        $user = User::create([
            'name' => 'Limited Buyer',
            'email' => 'limited-buyer@example.com',
            'password' => 'StrongPassword123!',
            'is_admin' => false,
            'loyalty_points' => 50,
        ]);
        $product = $this->product(['price' => 25.50, 'stock' => 5]);

        $this->actingAs($user)->withSession([
            'cart' => [(string) $product->id => 1],
        ])->post('/checkout', [
            'customer_name' => 'Limited Buyer',
            'phone' => '555123456',
            'city' => 'Tbilisi',
            'address' => 'Test Street 1',
            'payment_method' => 'cash_on_delivery',
            'loyalty_points_to_redeem' => 51,
        ])->assertSessionHasErrors('loyalty_points_to_redeem');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(50, (int) $user->fresh()->loyalty_points);
        $this->assertDatabaseCount('loyalty_point_transactions', 0);
    }

    public function test_checkout_does_not_create_order_when_stock_is_insufficient(): void
    {
        $product = $this->product(['stock' => 1]);
        $this->withSession(['cart' => [(string) $product->id => 2]]);

        $this->post('/checkout', [
            'customer_name' => 'Test Buyer',
            'phone' => '555123456',
            'city' => 'Tbilisi',
            'address' => 'Test Street 1',
            'payment_method' => 'cash_on_delivery',
        ])->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 1,
        ]);
    }
}
