@extends('layouts.store')
@section('title','შეკვეთის გაფორმება — VENTOR')
@section('content')
<section class="section checkout-section"><div class="wrap">
    <div class="eyebrow">VENTOR / SECURE CHECKOUT</div><h1>შეკვეთის <span class="text-blue">გაფორმება.</span></h1>
    @if($errors->any())<div class="notice" role="alert">{{ $errors->first() }}</div>@endif
    <div class="checkout-layout">
        <form class="checkout-form" method="POST" action="{{ route('checkout.store') }}">@csrf
            <div class="checkout-form-heading"><span class="step-number">01</span><div><h2>მიწოდების ინფორმაცია</h2><p class="muted">შეავსე საკონტაქტო და მისამართის დეტალები.</p></div></div>
            <div class="checkout-fields">
                <label class="form-field form-field-full"><span>სახელი და გვარი</span><input class="field" name="customer_name" value="{{ old('customer_name') }}" required maxlength="120" autocomplete="name"></label>
                <label class="form-field"><span>ტელეფონი</span><input class="field" type="tel" name="phone" value="{{ old('phone') }}" required maxlength="40" autocomplete="tel"></label>
                <label class="form-field"><span>ელფოსტა <i>(არასავალდებულო)</i></span><input class="field" type="email" name="email" value="{{ old('email') }}" autocomplete="email"></label>
                <label class="form-field"><span>ქალაქი</span><input class="field" name="city" value="{{ old('city') }}" required maxlength="100" autocomplete="address-level2"></label>
                <label class="form-field"><span>მისამართი</span><input class="field" name="address" value="{{ old('address') }}" required maxlength="255" autocomplete="street-address"></label>
            </div>
            <div class="checkout-form-heading checkout-second-heading"><span class="step-number">02</span><div><h2>გადახდის მეთოდი</h2><p class="muted">აირჩიე შენთვის მოსახერხებელი გადახდის ფორმა.</p></div></div>
            <div class="payment-options">
                <label class="payment-option"><input type="radio" name="payment_method" value="cash_on_delivery" @checked(old('payment_method','cash_on_delivery')==='cash_on_delivery') required><span><strong>კურიერთან ნაღდი ანგარიშსწორება</strong><small>გადახდა შეკვეთის მიღებისას</small></span></label>
                <label class="payment-option"><input type="radio" name="payment_method" value="bank_transfer" @checked(old('payment_method')==='bank_transfer')><span><strong>საბანკო გადარიცხვა</strong><small>გადარიცხვის დეტალები შეგეთანხმება</small></span></label>
            </div>
            <label class="form-field checkout-note"><span>კომენტარი <i>(არასავალდებულო)</i></span><textarea class="field" name="note" rows="3">{{ old('note') }}</textarea></label>
            <button class="btn checkout-submit" type="submit">შეკვეთის დადასტურება <span>↗</span></button>
            <p class="checkout-privacy">შეკვეთის გაგზავნით ადასტურებ, რომ მითითებული საკონტაქტო ინფორმაცია სწორია.</p>
        </form>
        <aside class="checkout-summary"><div class="eyebrow">YOUR ORDER</div><h2>შეკვეთის შემადგენლობა</h2>
            @foreach($lines as $line)<div class="checkout-summary-item"><div><strong>{{ $line['product']->name }}</strong><span class="muted">რაოდენობა: {{ $line['quantity'] }}</span></div><strong>{{ number_format($line['line_total'],2) }} ₾</strong></div>@endforeach
            <div class="summary-total"><span>სულ</span><strong>{{ number_format($total,2) }} ₾</strong></div><p class="summary-note">მიწოდების საფასური ამ ეტაპზე არ არის დათვლილი. საჭიროების შემთხვევაში წინასწარ დაგიკავშირდებით.</p><a class="text-link" href="{{ route('cart.index') }}">← კალათის რედაქტირება</a>
        </aside>
    </div>
</div></section>
@endsection