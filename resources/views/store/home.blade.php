@extends('layouts.store')

@push('head')<link rel="stylesheet" href="{{ asset('css/storefront-premium.css') }}">@endpush
@section('title', 'VENTOR — პრემიუმ მოვლა ავტომობილისა და მოტოციკლისთვის')
@section('meta_description', 'პრემიუმ საავტომობილო და მოტო მოვლის საშუალებები ერთ სივრცეში. აღმოაჩინე VENTOR-ის შერჩეული პროდუქცია.')
@section('canonical_url', route('home'))

@section('content')
<section class="home-hero">
    <div class="hero-image" aria-hidden="true"></div>
    <div class="hero-shade" aria-hidden="true"></div>
    <div class="wrap hero-inner">
        <div class="hero-copy">
            <div class="eyebrow"><span class="eyebrow-line"></span> PREMIUM AUTOMOTIVE CARE · GEORGIA</div>
            <h1>შენი ავტომობილი.<br><span>უმაღლესი სტანდარტი.</span></h1>
            <p>პრემიუმ მოვლის საშუალებები მათთვის, ვინც შედეგს დეტალებში ამჩნევს. აღმოაჩინე ხარისხი, რომელსაც შენი ავტომობილი იმსახურებს.</p>
            <div class="hero-actions">
                <a class="btn" href="{{ route('catalog') }}">პროდუქციის ნახვა <span aria-hidden="true">↗</span></a>
                <a class="hero-text-link" href="#collections">კოლექციების აღმოჩენა <span aria-hidden="true">↓</span></a>
            </div>
            <div class="hero-proof">
                <span><i>01</i> შერჩეული ხარისხი</span>
                <span><i>02</i> ავტომობილი და მოტო</span>
                <span><i>03</i> ზრუნვა დეტალებზე</span>
            </div>
        </div>
        <div class="hero-side-note"><span>VENTOR</span><span>CARE IN EVERY DETAIL</span></div>
    </div>
    <div class="hero-bottom-line"><span>01 / 03</span><span>DRIVE THE DIFFERENCE</span></div>
</section>

<section class="benefit-strip">
    <div class="wrap benefit-grid">
        <div><span class="benefit-icon">✦</span><span><strong>პრემიუმ არჩევანი</strong><small>შერჩეული მოვლის საშუალებები</small></span></div>
        <div><span class="benefit-icon">◈</span><span><strong>ავტო & მოტო</strong><small>ზრუნვა ყველა ტიპის ტრანსპორტზე</small></span></div>
        <div><span class="benefit-icon">↗</span><span><strong>მარტივი შერჩევა</strong><small>მოძებნე შენთვის საჭირო პროდუქტი</small></span></div>
    </div>
</section>

<section class="section home-collections" id="collections">
    <div class="wrap">
        <div class="section-head">
            <div><div class="eyebrow">01 / EXPLORE COLLECTIONS</div><h2>იპოვე შენი მოვლის სტანდარტი</h2></div>
            <p class="section-intro">ყველაფერი, რაც სისუფთავეს, დაცვასა და მოვლას სჭირდება — ერთ სივრცეში.</p>
        </div>
        <div class="collection-grid">
            @forelse($categories as $category)
                <a class="collection-card" href="{{ route('catalog', ['category' => $category->slug]) }}">
                    <span class="collection-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="collection-arrow" aria-hidden="true">↗</span>
                    <div class="collection-content">
                        <span class="eyebrow">VENTOR COLLECTION</span>
                        <h3>{{ $category->name }}</h3>
                        @if($category->description)<p>{{ $category->description }}</p>@endif
                        <span class="collection-link">კატეგორიის ნახვა <b>→</b></span>
                    </div>
                </a>
            @empty
                <div class="empty-collection"><span class="eyebrow">COMING INTO FOCUS</span><h3>კოლექციები მზადდება</h3><p>პროდუქციის კატეგორიები გამოჩნდება მათი დამატებისთანავე.</p></div>
            @endforelse
        </div>
    </div>
</section>

@php
    $homeProducts = $featured->isNotEmpty() ? $featured : $products;
    $showingFeatured = $featured->isNotEmpty();
@endphp
<section class="section home-products">
    <div class="wrap">
        <div class="section-head">
            <div><div class="eyebrow">02 / THE SELECTION</div><h2>{{ $showingFeatured ? 'VENTOR-ის რჩეული' : 'ახლახან დამატებული' }}</h2></div>
            <a class="section-more" href="{{ route('catalog') }}">მთელი კატალოგი <span>↗</span></a>
        </div>
        @if($homeProducts->isNotEmpty())
            <div class="grid product-grid">
                @foreach($homeProducts as $product)
                    @include('store.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @else
            <div class="empty-products"><div class="eyebrow">THE COLLECTION IS GROWING</div><h3>ახალი პროდუქტები გზაშია.</h3><p>როგორც კი პროდუქცია გამოქვეყნდება, აქ პირველივე ნახავ.</p><a class="btn secondary" href="{{ route('catalog') }}">კატალოგის ნახვა ↗</a></div>
        @endif
    </div>
</section>

<section class="home-search">
    <div class="wrap search-panel">
        <div><div class="eyebrow">FIND YOUR PERFECT CARE</div><h2>ეძებ კონკრეტულ პროდუქტს?</h2><p>მოძებნე სახელით, ბრენდით ან პროდუქტის კოდით.</p></div>
        <form class="home-search-form" method="GET" action="{{ route('catalog') }}">
            <label class="sr-only" for="home-search">პროდუქტის ძებნა</label>
            <input id="home-search" type="search" name="q" placeholder="მაგ. შამპუნი, ცვილი, ბრენდი..." value="{{ request('q') }}">
            <button class="btn" type="submit">ძიება <span aria-hidden="true">↗</span></button>
        </form>
    </div>
</section>

<section class="section home-about" id="about">
    <div class="wrap about-grid">
        <div><div class="eyebrow">03 / THE VENTOR STANDARD</div><h2>ზრუნვა, რომელიც<br><span>განსხვავებას ქმნის.</span></h2></div>
        <div class="about-copy"><p>VENTOR აერთიანებს ავტომობილისა და მოტოციკლის მოვლის პროდუქტებს. ჩვენი მიდგომა მარტივია — ხარისხიანი არჩევანი, ზუსტი ინფორმაცია და ყურადღება თითოეულ დეტალზე.</p><a class="hero-text-link" href="{{ route('catalog') }}">აღმოაჩინე VENTOR <span>↗</span></a></div>
    </div>
</section>
@endsection
