<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->where('is_active', true)->get()->keyBy('id');
        $lines = collect($cart)->map(function ($quantity, $id) use ($products) {
            $product = $products->get($id);

            if (! $product || $product->stock < 1) {
                return null;
            }

            $quantity = min(max(1, (int) $quantity), (int) $product->stock);

            return [
                'product' => $product,
                'quantity' => $quantity,
                'line_total' => (float) $product->price * $quantity,
                'stock' => (int) $product->stock,
            ];
        })->filter()->values();

        $normalizedCart = $lines->mapWithKeys(fn ($line) => [
            (string) $line['product']->id => $line['quantity'],
        ])->all();

        if ($normalizedCart !== $cart) {
            $request->session()->put('cart', $normalizedCart);
            session()->flash('cart_adjusted', 'კალათა განახლდა: მიუწვდომელი პროდუქტები წაიშალა და რაოდენობები მარაგს მოერგო.');
        }

        return view('store.cart', [
            'lines' => $lines,
            'total' => $lines->sum('line_total'),
            'itemCount' => $lines->sum('quantity'),
        ]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        $product = Product::whereKey($data['product_id'])
            ->where('is_active', true)
            ->firstOrFail();

        if ($product->stock < 1) {
            throw ValidationException::withMessages(['cart' => 'ეს პროდუქტი ამჟამად მარაგში არ არის.']);
        }

        $cart = $request->session()->get('cart', []);
        $quantity = (int) ($cart[$product->id] ?? 0) + (int) $data['quantity'];

        if ($quantity > min(30, (int) $product->stock)) {
            throw ValidationException::withMessages([
                'cart' => 'არჩეული რაოდენობა მარაგს აღემატება. ხელმისაწვდომია: '.$product->stock.' ცალი.',
            ]);
        }

        $cart[$product->id] = $quantity;
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'პროდუქტი კალათაში დაემატა.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'quantities' => ['required', 'array', 'min:1'],
            'quantities.*' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        $cart = $request->session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($data['quantities']))
            ->where('is_active', true)->get()->keyBy('id');

        $updated = $cart;

        foreach ($data['quantities'] as $id => $quantity) {
            $product = $products->get($id);

            if (! $product) {
                throw ValidationException::withMessages([
                    'cart' => 'ერთ-ერთი პროდუქტი აღარ არის ხელმისაწვდომი. განაახლე გვერდი.',
                ]);
            }

            if ($quantity > $product->stock) {
                throw ValidationException::withMessages([
                    'cart' => $product->name.' — მარაგში ხელმისაწვდომია მხოლოდ '.$product->stock.' ცალი.',
                ]);
            }

            $updated[$id] = (int) $quantity;
        }

        $request->session()->put('cart', $updated);

        return back()->with('success', 'კალათა წარმატებით განახლდა.');
    }

    public function remove(Request $request, Product $product)
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$product->id]);
        $request->session()->put('cart', $cart);

        return back()->with('success', 'პროდუქტი კალათიდან წაიშალა.');
    }
}
