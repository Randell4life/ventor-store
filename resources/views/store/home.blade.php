@extends('layouts.store')
@section('title', 'VENTOR — ხარისხი ყველა დეტალში')
@section('content')
<section class="hero"><div class="wrap"><div class="eyebrow">PREMIUM AUTOMOTIVE CARE</div><h1>ზრუნვა, რომელიც<br><span style="color:var(--ice)">ყველა დეტალში</span> იგრძნობა.</h1><p>ავტომობილისა და მოტოციკლის მოვლის პროდუქტები მათთვის, ვინც ხარისხს კომპრომისის გარეშე ირჩევს.</p><a class="btn" href="{{ route('catalog') }}">პროდუქციის ნახვა</a></div></section>
<section class="section"><div class="wrap"><div class="section-head"><div><div class="eyebrow">EXPLORE</div><h2>კატეგორიები</h2></div></div><div class="grid">@forelse($categories as $category)<a class="category" href="{{ route('catalog',['category'=>$category->slug]) }}">{{ $category->name }} <span style="float:right;color:var(--blue)">↗</span></a>@empty<p class="muted">კატეგორიები ჯერ არ არის დამატებული.</p>@endforelse</div></div></section>
<section class="section" style="background:#0b111a"><div class="wrap"><div class="section-head"><div><div class="eyebrow">SELECTED PRODUCTS</div><h2>რჩეული პროდუქცია</h2></div><a href="{{ route('catalog') }}" class="muted">ყველას ნახვა →</a></div><div class="grid">@forelse($featured as $product)@include('store.partials.product-card',['product'=>$product])@empty<p class="muted">რჩეული პროდუქტები ჯერ არ არის მონიშნული ადმინისტრაციაში.</p>@endforelse</div></div></section>
@endsection
