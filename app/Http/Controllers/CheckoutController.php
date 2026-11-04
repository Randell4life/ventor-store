<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    private function cartLines(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->where('is_active', true)->get()->keyBy('id');

        return collect($cart)->map(function ($quantity, $id) use ($products) {
            $product = $products->get($id);
            if (!$product) {
                return null;
            }

            $quantity = (int) $quantity;

            return [
                'product' => $product,
                'quantity' => $quantity,
                'line_total' => (float) $product->price * $quantity,
                'savings' => $product->hasActiveDiscount() ? ((float) $product->compare_at_price - (float) $product->price) * $quantity : 0,
            ];
        })->filter()->values();
    }

    public function applyCoupon(Request $request)
    {
        $data = $request->validate(['coupon_code' => ['required', 'string', 'max:50']]);
        $subtotal = (float) $this->cartLines($request)->sum('line_total');
        $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper(trim($data['coupon_code']))])->first();

        if (! $coupon || ! $coupon->isUsableFor($subtotal)) {
            return back()->withErrors(['coupon_code' => 'კუპონი არასწორია ან მისი პირობები არ სრულდება.']);
        }

        $request->session()->put('coupon_code', strtoupper($coupon->code));
        return back()->with('coupon_success', 'პრომოკოდი გააქტიურდა.');
    }

    public function removeCoupon(Request $request)
    {
        $request->session()->forget('coupon_code');
        return back()->with('coupon_success', 'პრომოკოდი მოხსნილია.');
    }

    public function create(Request $request)
    {
        $lines = $this->cartLines($request);

        if ($lines->isEmpty()) {
            return redirect()->route('cart.index')
                ->withErrors(['cart' => 'შეკვეთის გასაფორმებლად კალათაში პროდუქტი დაამატე.']);
        }

        $subtotal = (float) $lines->sum('line_total');
        $coupon = null;
        $couponDiscount = 0.0;
        if ($code = $request->session()->get('coupon_code')) {
            $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper($code)])->first();
            if ($coupon && $coupon->isUsableFor($subtotal)) {
                $couponDiscount = $coupon->calculateDiscount($subtotal);
            } else {
                $request->session()->forget('coupon_code');
                $coupon = null;
            }
        }

        $availablePoints = auth()->user() ? (int) auth()->user()->loyalty_points : 0;
        $maxRedeemablePoints = min($availablePoints, (int) floor(max(0, $subtotal - $couponDiscount) * 30));

        return view('store.checkout', [
            'lines' => $lines,
            'subtotal' => $subtotal,
            'total' => max(0, $subtotal - $couponDiscount),
            'savings' => $lines->sum('savings'),
            'coupon' => $coupon,
            'couponDiscount' => $couponDiscount,
            'availablePoints' => $availablePoints,
            'maxRedeemablePoints' => $maxRedeemablePoints,
        ]);
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
            'loyalty_points_to_redeem' => ['nullable', 'integer', 'min:0'],
        ]);

        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            throw ValidationException::withMessages([
                'cart' => 'კალათა ცარიელია.',
            ]);
        }

        $order = DB::transaction(function () use ($data, $cart, $request) {
            $subtotal = 0;
            $lines = [];

            foreach ($cart as $id => $quantity) {
                $quantity = (int) $quantity;
                $product = Product::whereKey($id)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->first();

                if (!$product || $quantity < 1 || $product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'cart' => 'პროდუქტი აღარ არის ხელმისაწვდომი მოთხოვნილი რაოდენობით. განაახლე კალათა.',
                    ]);
                }

                $lineTotal = (float) $product->price * $quantity;
                $subtotal += $lineTotal;
                $lines[] = [$product, $quantity, $lineTotal];
            }

            $coupon = null;
            $couponDiscount = 0.0;
            if ($code = $request->session()->get('coupon_code')) {
                $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper($code)])->lockForUpdate()->first();
                if (! $coupon || ! $coupon->isUsableFor($subtotal)) {
                    throw ValidationException::withMessages(['coupon_code' => 'პრომოკოდის პირობები აღარ სრულდება.']);
                }
                $couponDiscount = $coupon->calculateDiscount($subtotal);
                $coupon->increment('used_count');
            }

            $pointsToRedeem = (int) ($data['loyalty_points_to_redeem'] ?? 0);
            $pointsDiscount = 0.0;

            if ($pointsToRedeem > 0) {
                if (! auth()->check()) {
                    throw ValidationException::withMessages(['loyalty_points_to_redeem' => 'ქულების გამოსაყენებლად შედი შენს ანგარიშში.']);
                }

                $user = auth()->user()->newQuery()->whereKey(auth()->id())->lockForUpdate()->firstOrFail();
                $maxPoints = min((int) $user->loyalty_points, (int) floor(max(0, $subtotal - $couponDiscount) * 30));

                if ($pointsToRedeem > $maxPoints) {
                    throw ValidationException::withMessages(['loyalty_points_to_redeem' => 'ქულების რაოდენობა შეიცვალა ან აჭარბებს დასაშვებ ლიმიტს.']);
                }

                $pointsDiscount = $pointsToRedeem / 100;
                $user->decrement('loyalty_points', $pointsToRedeem);
                $user->loyaltyPointTransactions()->create([
                    'type' => 'redeemed',
                    'points' => $pointsToRedeem,
                    'balance_after' => (int) $user->fresh()->loyalty_points,
                    'description' => 'ქულების გამოყენება შეკვეთაზე',
                ]);
            }

            $order = Order::create([
                ...$data,
                'user_id' => auth()->id(),
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'discount_amount' => $couponDiscount + $pointsDiscount,
                'loyalty_points_redeemed' => $pointsToRedeem,
                'order_number' => 'VNT-'.now()->format('ymd').'-'.Str::upper(Str::random(5)),
                'public_token' => Str::random(64),
                'subtotal' => $subtotal,
                'delivery_fee' => 0,
                'total' => max(0, $subtotal - $couponDiscount - $pointsDiscount),
                'status' => 'new',
            ]);

            foreach ($lines as [$product, $quantity, $lineTotal]) {
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'line_total' => $lineTotal,
                ]);

                $stockBefore = (int) $product->stock;
                $product->decrement('stock', $quantity);

                $product->inventoryMovements()->create([
                    'user_id' => auth()->id(),
                    'movement_type' => 'sale',
                    'quantity_change' => -$quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockBefore - $quantity,
                    'reference' => $order->order_number,
                    'note' => 'ონლაინ შეკვეთა',
                ]);
            }

            return $order;
        });

        $request->session()->forget(['cart', 'coupon_code']);

        return redirect()->route('checkout.success', [
            'token' => $order->public_token,
        ]);
    }

    public function success(string $token)
    {
        $order = Order::where('public_token', $token)
            ->with('items')
            ->firstOrFail();

        return view('store.success', compact('order'));
    }
}
