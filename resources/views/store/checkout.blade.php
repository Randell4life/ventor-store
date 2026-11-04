@extends('layouts.store')
@push('head')
<link rel="stylesheet" href="{{ asset('css/storefront-premium.css') }}">
<style>
.checkout-layout{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(300px,.9fr);gap:26px;align-items:start}
.checkout-form,.checkout-summary{padding:24px;background:var(--panel);border:1px solid var(--line)}
.checkout-summary{position:sticky;top:110px}
.checkout-form label{display:block;margin:15px 0 7px;color:var(--muted);font-size:12px}
.checkout-form .field{width:100%;min-height:46px}
.checkout-form textarea.field{min-height:95px}
.checkout-fields{display:grid;grid-template-columns:1fr 1fr;gap:0 14px}
.checkout-fields .full{grid-column:1/-1}
.checkout-step{display:flex;gap:12px;align-items:center;margin-bottom:20px}
.checkout-step span{display:grid;place-items:center;width:30px;height:30px;border:1px solid #3779b8;color:var(--ice);font-size:12px}
.checkout-summary-line{display:flex;justify-content:space-between;gap:12px;padding:13px 0;border-bottom:1px solid var(--line);font-size:13px}
.checkout-security{font-size:11px;line-height:1.7;color:var(--muted);margin-top:14px}
.checkout-error{padding:12px 14px;margin:14px 0;background:#2a1717;border:1px solid #633838;color:#ffc1c1}
@media(max-width:850px){.checkout-layout{grid-template-columns:1fr}.checkout-summary{position:relative;top:auto}}
@media(max-width:560px){.checkout-form,.checkout-summary{padding:16px}.checkout-fields{grid-template-columns:1fr}.checkout-fields .full{grid-column:auto}}
</style>
@endpush
@section('title','შეკვეთის გაფორმება — VENTOR')
@section('content')
<section class="section">
    <div class="wrap">
        <div class="eyebrow">SECURE CHECKOUT</div>
        <h1 style="font-size:clamp(34px,5vw,52px)">შეკვეთის გაფორმება</h1>
        <p class="muted">შეავსე მიმღების ინფორმაცია და გადაამოწმე შეკვეთა.</p>

        <div class="checkout-layout" style="margin-top:25px">
            <form class="checkout-form" method="POST" action="{{ route('checkout.store') }}">
                @csrf
                <div class="checkout-step"><span>01</span><div><strong>მიმღების ინფორმაცია</strong><div class="muted" style="font-size:11px">საკონტაქტო და მიწოდების დეტალები</div></div></div>
                @if($errors->any())<div class="checkout-error">{{ $errors->first() }}</div>@endif
                <div class="checkout-fields">
                    <div class="full"><label for="customer_name">სახელი და გვარი *</label><input id="customer_name" class="field" name="customer_name" value="{{ old('customer_name',auth()->user()?->name) }}" autocomplete="name" required maxlength="120"></div>
                    <div><label for="phone">ტელეფონი *</label><input id="phone" class="field" type="tel" name="phone" value="{{ old('phone',auth()->user()?->phone) }}" autocomplete="tel" required maxlength="40"></div>
                    <div><label for="email">ელფოსტა</label><input id="email" class="field" type="email" name="email" value="{{ old('email',auth()->user()?->email) }}" autocomplete="email" maxlength="150"></div>
                    <div><label for="city">ქალაქი *</label><input id="city" class="field" name="city" value="{{ old('city') }}" autocomplete="address-level2" required maxlength="100"></div>
                    <div><label for="address">მისამართი *</label><input id="address" class="field" name="address" value="{{ old('address') }}" autocomplete="street-address" required maxlength="255"></div>
                    <div class="full"><label for="payment_method">გადახდის მეთოდი *</label><select id="payment_method" class="field" name="payment_method" required><option value="cash_on_delivery" @selected(old('payment_method','cash_on_delivery')==='cash_on_delivery')>კურიერთან ნაღდი ანგარიშსწორება</option><option value="bank_transfer" @selected(old('payment_method')==='bank_transfer')>საბანკო გადარიცხვა</option></select></div>
                    <div class="full"><label for="note">დამატებითი კომენტარი</label><textarea id="note" class="field" name="note" rows="3" maxlength="1000" placeholder="მაგ. სადარბაზო, სართული ან მიწოდების მითითება">{{ old('note') }}</textarea></div>
                </div>
                <button class="btn" style="width:100%;margin-top:22px" type="submit">შეკვეთის დადასტურება →</button>
                <p class="checkout-security">შეკვეთის დადასტურებისას სისტემა ხელახლა ამოწმებს ფასებსა და მარაგს. შეკვეთის გაფორმება არ ნიშნავს ბარათით გადახდას — ამ ეტაპზე ხელმისაწვდომია ადგილზე გადახდა ან საბანკო გადარიცხვა.</p>
            </form>

            <aside class="checkout-summary">
                <div class="eyebrow">ORDER SUMMARY</div>
                <h2 style="margin:8px 0 15px">შენი შეკვეთა</h2>
                @foreach($lines as $line)
                    <div class="checkout-summary-line">
                        <div><strong>{{ $line['product']->name }}</strong><div class="muted" style="font-size:11px;margin-top:4px">{{ $line['quantity'] }} × {{ number_format((float)$line['product']->price,2) }} ₾</div></div>
                        <strong>{{ number_format($line['line_total'],2) }} ₾</strong>
                    </div>
                @endforeach
                <div class="checkout-summary-line"><span class="muted">პროდუქტების ჯამი</span><strong>{{ number_format($total,2) }} ₾</strong></div>
                @if($savings > 0)<div class="checkout-summary-line"><span style="color:#8eddb5">ფასდაკლებით დაზოგე</span><strong style="color:#8eddb5">−{{ number_format($savings,2) }} ₾</strong></div>@endif
                <div class="checkout-summary-line"><span class="muted">მიწოდება</span><span class="muted">შეთანხმებით</span></div>
                <div style="display:flex;justify-content:space-between;gap:12px;padding:20px 0;font-size:21px"><strong>სულ</strong><strong>{{ number_format($total,2) }} ₾</strong></div>
                <a href="{{ route('cart.index') }}">← კალათის რედაქტირება</a>
            </aside>
        </div>
    </div>
</section>
@endsection
