@extends('layouts.store')
@section('title','კალათა — VENTOR')
@section('content')
<section class="section cart-section"><div class="wrap">
    <div class="eyebrow">YOUR SELECTION / 01</div><h1>შენი <span class="text-blue">კალათა.</span></h1>
    @if(session('success'))<div class="notice" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="notice" role="alert">{{ $errors->first() }}</div>@endif
    @if($lines->isEmpty())
        <div class="cart-empty"><div class="cart-empty-mark">V.</div><h2>კალათა ჯერ ცარიელია.</h2><p class="muted">შეარჩიე სასურველი პროდუქცია VENTOR-ის კატალოგიდან.</p><a class="btn" href="{{ route('catalog') }}">კატალოგის ნახვა ↗</a></div>
    @else
        <div class="cart-layout">
            <div class="cart-items-column">
                <div class="cart-column-heading"><span>პროდუქტი</span><span>{{ $lines->count() }} პოზიცია</span></div>
                <form id="cart-update-form" method="POST" action="{{ route('cart.update') }}">@csrf @method('PATCH')
                    @foreach($lines as $line)
                        <article class="cart-item">
                            <div class="cart-item-visual">@if($line['product']->image_path)<img src="{{ asset('storage/'.$line['product']->image_path) }}" alt="{{ $line['product']->name }}" loading="lazy">@else<div class="bottle" aria-hidden="true"></div>@endif</div>
                            <div class="cart-item-info"><div class="eyebrow">{{ $line['product']->category?->name ?? 'VENTOR COLLECTION' }}</div><h3><a href="{{ route('product.show', $line['product']) }}">{{ $line['product']->name }}</a></h3><span class="muted">{{ number_format((float)$line['product']->price,2) }} ₾ / ერთეული</span></div>
                            <label class="cart-quantity"><span>რაოდენობა</span><input class="field" type="number" min="1" max="30" name="quantities[{{ $line['product']->id }}]" value="{{ $line['quantity'] }}"></label>
                            <div class="cart-item-total"><strong>{{ number_format($line['line_total'],2) }} ₾</strong><button class="remove-link" type="submit" form="remove-{{ $line['product']->id }}">წაშლა ×</button></div>
                        </article>
                    @endforeach
                </form>
                @foreach($lines as $line)
                    <form id="remove-{{ $line['product']->id }}" method="POST" action="{{ route('cart.remove', $line['product']) }}">@csrf @method('DELETE')</form>
                @endforeach
                <div class="cart-actions"><a class="text-link" href="{{ route('catalog') }}">← კატალოგში დაბრუნება</a><button class="btn secondary" type="submit" form="cart-update-form">რაოდენობების განახლება</button></div>
            </div>
            <aside class="cart-summary"><div class="eyebrow">ORDER SUMMARY</div><h2>შეკვეთის შეჯამება</h2><div class="summary-line"><span>პროდუქციის ჯამი</span><strong>{{ number_format($total,2) }} ₾</strong></div><div class="summary-line muted"><span>მიწოდება</span><span>დაზუსტდება</span></div><div class="summary-total"><span>სულ</span><strong>{{ number_format($total,2) }} ₾</strong></div><a class="btn checkout-button" href="{{ route('checkout') }}">შეკვეთის გაფორმება <span>↗</span></a><p class="summary-note">შეკვეთის დადასტურების შემდეგ დეტალებს შეგითანხმებთ.</p></aside>
        </div>
    @endif
</div></section>
@endsection