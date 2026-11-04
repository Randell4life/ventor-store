<?php

namespace Tests\Feature;

use App\Models\Product;
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

        $order = \\App\\Models\\Order::firstOrFail();

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
