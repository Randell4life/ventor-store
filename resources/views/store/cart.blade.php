@extends('layouts.store')
@push('head')
<link rel="stylesheet" href="{{ asset('css/storefront-premium.css') }}">
@endpush
@section('title','კალათა — VENTOR')
@section('content')
<section class="section">
    <div class="wrap">
        <div class="eyebrow">YOUR CART</div>
        <h1>შენი კალათა</h1>

        @if(session('success'))
            <div class="notice">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="notice">{{ $errors->first() }}</div>
        @endif

        @if($lines->isEmpty())
            <p class="muted">კალათა ცარიელია.</p>
            <a class="btn" href="{{ route('catalog') }}">კატალოგში დაბრუნება</a>
        @else
            <form method="POST" action="{{ route('cart.update') }}">
                @csrf
                @method('PATCH')
                @foreach($lines as $line)
                    <div class="card" style="display:flex;justify-content:space-between;align-items:center;gap:20px;margin:12px 0;flex-wrap:wrap">
                        <div>
                            <strong>{{ $line['product']->name }}</strong>
                            <p class="muted">{{ number_format($line['product']->price, 2) }} ₾</p>
                        </div>
                        <input class="field" style="width:90px" type="number" min="1" max="30"
                               name="quantities[{{ $line['product']->id }}]" value="{{ $line['quantity'] }}">
                        <strong>{{ number_format($line['line_total'], 2) }} ₾</strong>
                        <button class="btn secondary" type="submit" form="remove-{{ $line['product']->id }}">წაშლა</button>
                    </div>
                @endforeach
                <button class="btn secondary" type="submit">კალათის განახლება</button>
            </form>

            @foreach($lines as $line)
                <form id="remove-{{ $line['product']->id }}" method="POST"
                      action="{{ route('cart.remove', $line['product']) }}">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach

            <h2>ჯამი: {{ number_format($total, 2) }} ₾</h2>
            <a class="btn" href="{{ route('checkout') }}">შეკვეთის გაფორმება</a>
        @endif
    </div>
</section>
@endsection
