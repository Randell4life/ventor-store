@extends('layouts.store')
@section('title','შეკვეთების ისტორია — VENTOR')
@section('content')
<section class="section"><div class="wrap"><div class="eyebrow">ORDER HISTORY</div><h1>ჩემი შეკვეთები</h1>
@if($orders->isEmpty())<div class="card"><p class="muted">შეკვეთები ჯერ არ გაქვს.</p><a class="btn" href="{{ route('catalog') }}">კატალოგის ნახვა</a></div>@else
<div class="card">@foreach($orders as $order)<div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;padding:17px 0;border-bottom:1px solid var(--line)"><div><strong>{{ $order->order_number }}</strong><p class="muted">{{ $order->created_at->format('d.m.Y H:i') }} · {{ $order->items_count }} პროდუქტი · {{ $order->status }}</p></div><strong>{{ number_format((float)$order->total,2) }} ₾</strong><a href="{{ route('account.orders.show',$order) }}">დეტალები →</a></div>@endforeach</div>
<div class="pagination">{{ $orders->links() }}</div>@endif
<a href="{{ route('account.index') }}">← ანგარიშზე დაბრუნება</a></div></section>
@endsection