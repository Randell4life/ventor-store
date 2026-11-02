@extends('layouts.store')
@section('title','შეკვეთა მიღებულია — VENTOR')
@section('content')
<section class="section"><div class="wrap" style="max-width:700px"><div class="eyebrow">ORDER RECEIVED</div><h1>გმადლობთ შეკვეთისთვის.</h1><p class="muted">თქვენი შეკვეთის ნომერია:</p><h2 style="color:var(--ice)">{{ $order->order_number }}</h2><p>შეკვეთის სტატუსი: {{ $order->status }}</p><a class="btn" href="{{ route('catalog') }}">კატალოგში დაბრუნება</a></div></section>
@endsection
