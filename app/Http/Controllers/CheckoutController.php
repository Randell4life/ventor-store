<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function create()
    {
        return view('store.checkout');
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        $order = DB::transaction(function () use ($data) {
            $subtotal = 0;
            $lines = [];
            foreach ($data['items'] as $item) {
                $product = Product::whereKey($item['product_id'])->where('is_active', true)->lockForUpdate()->first();
                if (!$product || $product->stock < $item['quantity']) {
                    throw ValidationException::withMessages(['items' => 'ერთ ან მეტ პროდუქტზე მარაგი საკმარისი არ არის.']);
                }
                $lineTotal = (float) $product->price * $item['quantity'];
                $subtotal += $lineTotal;
                $lines[] = [$product, $item['quantity'], $lineTotal];
            }

            $order = Order::create([
                ...collect($data)->except('items')->all(),
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

        return redirect()->route('checkout.success', $order)->with('order_number', $order->order_number);
    }

    public function success(Order $order)
    {
        return view('store.success', compact('order'));
    }
}
