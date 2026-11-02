<?php

namespace App\\Http\\Controllers;

use App\\Models\\Order;
use App\\Models\\Product;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Str;
use Illuminate\\Validation\\ValidationException;

class CheckoutController extends Controller
{
    private function cartLines(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->where('is_active', true)->get()->keyBy('id');
        return collect($cart)->map(function ($quantity, $id) use ($products) {
            $product = $products->get($id);
            if (!$product) return null;
            $quantity = (int) $quantity;
            return ['product' => $product, 'quantity' => $quantity, 'line_total' => (float) $product->price * $quantity];
        })->filter()->values();
    }

    public function create(Request $request)
    {
        $lines = $this->cartLines($request);
        if ($lines->isEmpty()) return redirect()->route('cart.index')->withErrors(['cart' => 'შეკვეთის გასაფორმებლად კალათაში პროდუქტი დაამატე.']);
        return view('store.checkout', ['lines' => $lines, 'total' => $lines->sum('line_total')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cash_on_delivery,bank_transfer'],
        ]);
        $cart = $request->session()->get('cart', []);
        if (!$cart) throw ValidationException::withMessages(['cart' => 'კალათა ცარიელია.']);

        $order = DB::transaction(function () use ($data, $cart) {
            $subtotal = 0;
            $lines = [];
            foreach ($cart as $id => $quantity) {
                $product = Product::whereKey($id)->where('is_active', true)->lockForUpdate()->first();
                if (!$product || $quantity < 1 || $product->stock < $quantity) {
                    throw ValidationException::withMessages(['cart' => 'პროდუქტი აღარ არის ხელმისაწვდომი მოთხოვნილი რაოდენობით. განაახლე კალათა.']);
                }
                $lineTotal = (float) $product->price * (int) $quantity;
                $subtotal += $lineTotal;
                $lines[] = [$product, (int) $quantity, $lineTotal];
            }
            $order = Order::create([
                ...$data,
                'order_number' => 'VNT-'.now()->format('ymd').'-'.Str::upper(Str::random(5)),
                'subtotal' => $subtotal, 'delivery_fee' => 0, 'total' => $subtotal,
                'status' => 'new',
            ]);
            foreach ($lines as [$product, $quantity, $lineTotal]) {
                $order->items()->create([
                    'product_id' => $product->id, 'product_name' => $product->name,
                    'sku' => $product->sku, 'quantity' => $quantity,
                    'unit_price' => $product->price, 'line_total' => $lineTotal,
                ]);
                $product->decrement('stock', $quantity);
            }
            return $order;
        });

        $request->session()->forget('cart');
        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order)
    {
        return view('store.success', compact('order'));
    }
}
