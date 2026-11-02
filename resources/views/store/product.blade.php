@extends('layouts.store')
@section('title',$product->name.' — VENTOR')
@section('content')
<section class="section"><div class="wrap product-layout"><div class="visual">@if($product->image_path)<img src="{{ asset('storage/'.$product->image_path) }}" style="width:100%;height:100%;object-fit:contain">@else<div class="bottle" style="transform:scale(1.6)"></div>@endif</div><div><div class="eyebrow">{{ $product->category?->name ?? 'VENTOR' }}</div><h1 style="font-size:38px">{{ $product->name }}</h1><p class="muted">{{ $product->short_description }}</p><div class="price">{{ number_format((float)$product->price,2) }} ₾</div><p class="muted">მარაგი: {{ $product->stock > 0 ? $product->stock.' ცალი' : 'დროებით არ არის' }}</p><p style="line-height:1.9">{{ $product->description }}</p>
@if($product->stock > 0)<form method="POST" action="{{ route('cart.add') }}">@csrf<input type="hidden" name="product_id" value="{{ $product->id }}"><label>რაოდენობა</label><input class="field" style="width:110px" type="number" name="quantity" min="1" max="{{ min(30,$product->stock) }}" value="1"><button class="btn" type="submit">კალათაში დამატება</button></form>@else<p class="notice">პროდუქტი ამჟამად არ არის მარაგში.</p>@endif
</div></div></section>
@endsection
