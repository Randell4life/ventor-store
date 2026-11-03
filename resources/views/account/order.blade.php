@extends('layouts.store')
@section('title','შეკვეთა '.$order->order_number.' — VENTOR')
@section('content')
<section class="section"><div class="wrap"><div class="eyebrow">ORDER DETAILS</div><h1>{{ $order->order_number }}</h1>
<div class="product-layout" style="align-items:start"><div class="card"><h2>შეკვეთის ინფორმაცია</h2><p>თარიღი: {{ $order->created_at->format('d.m.Y H:i') }}</p><p>სტატუსი: <strong>{{ $order->status }}</strong></p><p>მიმღები: {{ $order->customer_name }}</p><p>ტელეფონი: {{ $order->phone }}</p><p>მისამართი: {{ $order->city }}, {{ $order->address }}</p><p>გადახდა: {{ $order->payment_method }}</p>@if($order->note)<p>კომენტარი: {{ $order->note }}</p>@endif</div>
<div class="card"><h2>პროდუქტები</h2>@foreach($order->items as $item)<div style="display:flex;justify-content:space-between;gap:12px;padding:13px 0;border-bottom:1px solid var(--line)"><span>{{ $item->product_name }} × {{ $item->quantity }}</span><strong>{{ number_format((float)$item->line_total,2) }} ₾</strong></div>@endforeach
<p>ქვეჯამი: {{ number_format((float)$order->subtotal,2) }} ₾</p><p>მიწოდება: {{ number_format((float)$order->delivery_fee,2) }} ₾</p><h2>ჯამი: {{ number_format((float)$order->total,2) }} ₾</h2></div></div>
<p style="margin-top:24px"><a href="{{ route('account.orders') }}">← შეკვეთების სიაში დაბრუნება</a></p></div></section>
@endsection