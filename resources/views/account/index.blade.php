@extends('layouts.store')
@section('title','ჩემი ანგარიში — VENTOR')
@section('content')
<section class="section"><div class="wrap"><div class="eyebrow">MY VENTOR</div><h1>გამარჯობა, {{ auth()->user()->name }}</h1>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
<div class="card" style="display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap;margin:24px 0;padding:22px">
<div><div class="eyebrow">VENTOR REWARDS</div><h2 style="margin:6px 0">შენი ბონუს ბალანსი</h2><p class="muted" style="margin:0">ყოველ დასრულებულ შეკვეთაზე მიიღებ 1 ქულას ყოველ დახარჯულ 1 ₾-ზე.</p></div>
<div style="text-align:right"><strong style="font-size:32px">{{ number_format((int) auth()->user()->loyalty_points) }}</strong><div class="muted">ქულა</div></div>
</div>
<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(210px,1fr));margin:28px 0">
<a class="card" href="{{ route('account.orders') }}"><div class="eyebrow">ORDERS</div><h2>ჩემი შეკვეთები</h2><p class="muted">შეკვეთების ისტორია და დეტალები</p></a>
<a class="card" href="{{ route('account.rewards') }}"><div class="eyebrow">VENTOR REWARDS</div><h2>ქულების ისტორია</h2><p class="muted">დარიცხული და გამოყენებული ქულები</p></a>
<a class="card" href="{{ route('account.profile.edit') }}"><div class="eyebrow">PROFILE</div><h2>პროფილის მართვა</h2><p class="muted">პირადი და კომპანიის ინფორმაცია</p></a>
<a class="card" href="{{ route('catalog') }}"><div class="eyebrow">SHOP</div><h2>კატალოგი</h2><p class="muted">აღმოაჩინე VENTOR-ის პროდუქტები</p></a>
</div>
<div class="section-head"><h2>ბოლო შეკვეთები</h2><a href="{{ route('account.orders') }}">ყველას ნახვა →</a></div>
@if($orders->isEmpty())<p class="muted">შეკვეთები ჯერ არ გაქვს.</p>@else
<div class="card">@foreach($orders as $order)<div style="display:flex;justify-content:space-between;gap:15px;flex-wrap:wrap;padding:15px 0;border-bottom:1px solid var(--line)"><div><strong>{{ $order->order_number }}</strong><p class="muted">{{ $order->created_at->format('d.m.Y H:i') }} · {{ $order->status }}</p></div><strong>{{ number_format((float)$order->total,2) }} ₾</strong><a href="{{ route('account.orders.show',$order) }}">დეტალები →</a></div>@endforeach</div>
@endif
<form method="POST" action="{{ route('account.logout') }}" style="margin-top:28px">@csrf<button class="btn secondary" type="submit">ანგარიშიდან გამოსვლა</button></form>
</div></section>
@endsection