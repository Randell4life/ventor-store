@extends('layouts.store')
@section('title','შეკვეთა მიღებულია — VENTOR')
@section('content')
<section class="section order-success-section">
    <div class="wrap order-success-card">
        <div class="success-symbol"><span>✓</span></div>
        <div class="eyebrow">VENTOR / ORDER RECEIVED</div>
        <h1>შეკვეთა <span class="text-blue">მიღებულია.</span></h1>
        <p class="success-lead">გმადლობთ, რომ აირჩიეთ VENTOR. თქვენი შეკვეთა წარმატებით დარეგისტრირდა.</p>
        <div class="success-order-number"><span>შეკვეთის ნომერი</span><strong>{{ $order->order_number }}</strong></div>
        <div class="success-status"><span>მიმდინარე სტატუსი</span><strong>{{ $order->status }}</strong></div>
        <a class="btn" href="{{ route('catalog') }}">კატალოგში დაბრუნება ↗</a>
    </div>
</section>
@endsection