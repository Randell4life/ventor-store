<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->where('is_active', true)->get()->keyBy('id');
        $lines = collect($cart)->map(function ($quantity, $id) use ($products) {
            $product = $products->get($id);
            return $product ? ['product' => $product, 'quantity' => (int) $quantity, 'line_total' => (float) $product->price * (int) $quantity] : null;
        })->filter()->values();
        return view('store.cart', ['lines' => $lines, 'total' => $lines->sum('line_total')]);
    }

    public function add(Request $request)
    {
        $data = $request->validate(['product_id' => ['required', 'integer', 'exists:products,id'], 'quantity' => ['required', 'integer', 'min:1', 'max:30']]);
        $product = Product::whereKey($data['product_id'])->where('is_active', true)->firstOrFail();
        $cart = $request->session()->get('cart', []);
        $quantity = (int) ($cart[$product->id] ?? 0) + (int) $data['quantity'];
        if ($quantity > $product->stock) return back()->withErrors(['cart' => 'არჩეული რაოდენობა მარაგში არ არის.']);
        $cart[$product->id] = $quantity;
        $request->session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'პროდუქტი კალათაში დაემატა.');
    }

    public function update(Request $request)
    {
        $data = $request->validate(['quantities' => ['required', 'array'], 'quantities.*' => ['required', 'integer', 'min:1', 'max:30']]);
        $cart = $request->session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($data['quantities']))->where('is_active', true)->get()->keyBy('id');
        foreach ($data['quantities'] as $id => $quantity) {
            $product = $products->get($id);
            if (!$product || $quantity > $product->stock) return back()->withErrors(['cart' => 'ერთ ან მეტ პროდუქტზე მოთხოვნილი რაოდენობა მარაგში არ არის.']);
            $cart[$id] = (int) $quantity;
        }
        $request->session()->put('cart', $cart);
        return back()->with('success', 'კალათა განახლდა.');
    }

    public function remove(Request $request, Product $product)
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$product->id]);
        $request->session()->put('cart', $cart);
        return back()->with('success', 'პროდუქტი კალათიდან წაიშალა.');
    }
}
