@extends('layouts.store')
@section('title','შეკვეთის გაფორმება — VENTOR')
@section('content')
<section class="section"><div class="wrap"><div class="eyebrow">SECURE CHECKOUT</div><h1>შეკვეთის გაფორმება</h1>
<div class="product-layout" style="align-items:start"><form method="POST" action="{{ route('checkout.store') }}">@csrf
@if($errors->any())<div class="notice">{{ $errors->first() }}</div>@endif
<label>სახელი და გვარი</label><input class="field" name="customer_name" value="{{ old('customer_name') }}" required maxlength="120">
<label>ტელეფონი</label><input class="field" name="phone" value="{{ old('phone') }}" required maxlength="40">
<label>ელფოსტა (არასავალდებულო)</label><input class="field" type="email" name="email" value="{{ old('email') }}">
<label>ქალაქი</label><input class="field" name="city" value="{{ old('city') }}" required maxlength="100">
<label>მისამართი</label><input class="field" name="address" value="{{ old('address') }}" required maxlength="255">
<label>გადახდის მეთოდი</label><select class="field" name="payment_method" required><option value="cash_on_delivery">კურიერთან ნაღდი ანგარიშსწორება</option><option value="bank_transfer">საბანკო გადარიცხვა</option></select>
<label>კომენტარი</label><textarea class="field" name="note" rows="3">{{ old('note') }}</textarea>
<button class="btn" type="submit">შეკვეთის დადასტურება</button></form>
<div class="card"><h2>შეკვეთის შემადგენლობა</h2>@foreach($lines as $line)<div style="display:flex;justify-content:space-between;gap:12px;padding:12px 0;border-bottom:1px solid var(--line)"><span>{{ $line['product']->name }} × {{ $line['quantity'] }}</span><strong>{{ number_format($line['line_total'],2) }} ₾</strong></div>@endforeach
<h2>ჯამი: {{ number_format($total,2) }} ₾</h2><p class="muted">მიწოდების საფასური ამ ეტაპზე არ არის დათვლილი და საჭიროების შემთხვევაში წინასწარ დაგიკავშირდებით.</p><a href="{{ route('cart.index') }}">კალათის რედაქტირება</a></div></div></div></section>
@endsection
