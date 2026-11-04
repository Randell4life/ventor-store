@extends('layouts.store')

@section('title', 'VENTOR — სარეცხი და საწმენდი საშუალებების წარმოება')
@section('meta_description', 'VENTOR აწარმოებს საავტომობილო, საყოფაცხოვრებო და ინდუსტრიულ სარეცხ და საწმენდ საშუალებებს. სრული საწარმოო ციკლი — ნედლეულიდან მზა პროდუქტამდე.')
@section('canonical_url', route('home'))

@section('content')
<section class="home-hero ventor-manufacturing-hero">
    <div class="hero-image" aria-hidden="true"></div>
    <div class="hero-shade" aria-hidden="true"></div>
    <div class="wrap hero-inner">
        <div class="hero-copy">
            <div class="eyebrow"><span class="eyebrow-line"></span> VENTOR · MANUFACTURING IN GEORGIA</div>
            <h1>სისუფთავის<br><span>შექმნილი სტანდარტი.</span></h1>
            <p>საავტომობილო, საყოფაცხოვრებო და ინდუსტრიული სარეცხი და საწმენდი საშუალებები — წარმოების სრული ციკლით, კონტროლირებული ხარისხითა და კონკურენტული ფასით.</p>
            <div class="hero-actions">
                <a class="btn" href="{{ route('catalog') }}">პროდუქციის ნახვა <span aria-hidden="true">↗</span></a>
                <a class="hero-text-link" href="#production">როგორ ვქმნით პროდუქტს <span aria-hidden="true">↓</span></a>
            </div>
            <div class="hero-proof">
                <span><i>01</i> სრული საწარმოო ციკლი</span>
                <span><i>02</i> სამი ძირითადი მიმართულება</span>
                <span><i>03</i> ფასი და ხარისხი</span>
            </div>
        </div>
        <div class="hero-side-note"><span>VENTOR</span><span>FROM RAW MATERIAL TO RESULT</span></div>
    </div>
    <div class="hero-bottom-line"><span>ENGINEERED CLEAN</span><span>QUALITY THROUGH CONTROL</span></div>
</section>

<section class="benefit-strip">
    <div class="wrap benefit-grid">
        <div><span class="benefit-icon">◈</span><span><strong>სრული კონტროლი</strong><small>წარმოების ყველა ეტაპზე</small></span></div>
        <div><span class="benefit-icon">✦</span><span><strong>ფასი და ხარისხი</strong><small>ოპტიმიზებული წარმოების ციკლი</small></span></div>
        <div><span class="benefit-icon">↗</span><span><strong>მრავალმხრივი გამოყენება</strong><small>ავტო, სახლი და ინდუსტრია</small></span></div>
    </div>
</section>

<section class="section home-collections" id="collections">
    <div class="wrap">
        <div class="section-head">
            <div><div class="eyebrow">01 / PRODUCT DIRECTIONS</div><h2>პროდუქცია სხვადასხვა საჭიროებისთვის</h2></div>
            <p class="section-intro">პრაქტიკული და ეფექტური გადაწყვეტილებები ყოველდღიური და პროფესიონალური სისუფთავისთვის.</p>
        </div>
        <div class="industry-grid">
            <a class="industry-card industry-auto" href="{{ route('catalog') }}">
                <span class="industry-index">01 / AUTOMOTIVE</span><div><span class="eyebrow">ავტომობილი</span><h3>ავტომობილის მოვლა</h3><p>სარეცხი და საწმენდი საშუალებები ავტომობილის ექსტერიერისა და ინტერიერისთვის.</p><span class="collection-link">პროდუქციის ნახვა <b>↗</b></span></div>
            </a>
            <a class="industry-card industry-home" href="{{ route('catalog') }}">
                <span class="industry-index">02 / HOME CARE</span><div><span class="eyebrow">საყოფაცხოვრებო</span><h3>სისუფთავე სახლში</h3><p>ყოველდღიური დასუფთავებისთვის განკუთვნილი ეფექტური საწმენდი საშუალებები.</p><span class="collection-link">პროდუქციის ნახვა <b>↗</b></span></div>
            </a>
            <a class="industry-card industry-industry" href="{{ route('catalog') }}">
                <span class="industry-index">03 / INDUSTRIAL</span><div><span class="eyebrow">ინდუსტრიული</span><h3>პროფესიონალური წმენდა</h3><p>საწარმოებისა და ბიზნესის საჭიროებებზე მორგებული საწმენდი გადაწყვეტილებები.</p><span class="collection-link">პროდუქციის ნახვა <b>↗</b></span></div>
            </a>
        </div>
    </div>
</section>

<section class="section production-section" id="production">
    <div class="wrap production-layout">
        <div class="production-copy">
            <div class="eyebrow">02 / OUR PRODUCTION CYCLE</div>
            <h2>ჩვენ ვაკონტროლებთ<br><span>პროცესს თავიდან ბოლომდე.</span></h2>
            <p>ჩვენი მთავარი უპირატესობა წარმოების სრული ციკლია. ნედლეულის შერჩევიდან მზა პროდუქტის შეფუთვამდე თითოეული ეტაპი ერთიან პროცესშია მოქცეული. ეს გვაძლევს ხარისხის კონტროლისა და ხარჯების ოპტიმიზაციის შესაძლებლობას.</p>
            <a class="hero-text-link" href="{{ route('catalog') }}">გაეცანი პროდუქციას <span>↗</span></a>
        </div>
        <div class="production-steps">
            <div class="production-step"><span>01</span><div><strong>ნედლეულის იმპორტი</strong><small>საჭირო კომპონენტების შერჩევა და მომარაგება</small></div></div>
            <div class="production-step"><span>02</span><div><strong>ნახევარპროდუქტების სინთეზი</strong><small>ფორმულებისა და ნარევების მომზადება</small></div></div>
            <div class="production-step"><span>03</span><div><strong>საბოლოო პროდუქტის შექმნა</strong><small>პროდუქტის ფორმულირება და ხარისხის კონტროლი</small></div></div>
            <div class="production-step"><span>04</span><div><strong>ტარის წარმოება</strong><small>შეფუთვისთვის საჭირო ტარის მომზადება</small></div></div>
            <div class="production-step"><span>05</span><div><strong>ჩამოსხმა და შეფუთვა</strong><small>მზა პროდუქტის შეფუთვა და რეალიზაციისთვის მომზადება</small></div></div>
        </div>
    </div>
</section>

@php
    $homeProducts = $featured->isNotEmpty() ? $featured : $products;
    $showingFeatured = $featured->isNotEmpty();
@endphp
<section class="section home-products" id="catalog">
    <div class="wrap">
        <div class="section-head">
            <div><div class="eyebrow">03 / VENTOR PRODUCTS</div><h2>{{ $showingFeatured ? 'რჩეული პროდუქცია' : 'ახლად დამატებული პროდუქცია' }}</h2></div>
            <a class="section-more" href="{{ route('catalog') }}">მთელი კატალოგი <span>↗</span></a>
        </div>
        @if($homeProducts->isNotEmpty())
            <div class="grid product-grid">
                @foreach($homeProducts as $product)
                    @include('store.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @else
            <div class="empty-products"><div class="eyebrow">VENTOR PRODUCT RANGE</div><h3>პროდუქციის კატალოგი ივსება.</h3><p>გამოქვეყნებული პროდუქცია ამ სივრცეში ავტომატურად გამოჩნდება.</p><a class="btn secondary" href="{{ route('catalog') }}">კატალოგის ნახვა ↗</a></div>
        @endif
    </div>
</section>

<section class="home-search">
    <div class="wrap search-panel">
        <div><div class="eyebrow">FIND THE RIGHT SOLUTION</div><h2>ეძებ კონკრეტულ პროდუქტს?</h2><p>მოძებნე პროდუქტის სახელით, ბრენდით ან კოდით.</p></div>
        <form class="home-search-form" method="GET" action="{{ route('catalog') }}">
            <label class="sr-only" for="home-search">პროდუქტის ძიება</label>
            <input id="home-search" type="search" name="q" placeholder="პროდუქტის სახელი ან კოდი..." value="{{ request('q') }}">
            <button class="btn" type="submit">ძიება <span aria-hidden="true">↗</span></button>
        </form>
    </div>
</section>

<section class="section home-about" id="about">
    <div class="wrap about-grid">
        <div><div class="eyebrow">04 / ABOUT VENTOR</div><h2>საუკეთესო ბალანსი<br><span>ფასსა და ხარისხს შორის.</span></h2></div>
        <div class="about-copy">
            <p>VENTOR სპეციალიზდება სარეცხი და საწმენდი საშუალებების წარმოებაში. ნედლეულის იმპორტის, ნახევარპროდუქტების სინთეზის, ტარის წარმოების, ჩამოსხმისა და შეფუთვის ერთიან პროცესში მოქცევა გვაძლევს შესაძლებლობას, მომხმარებელს შევთავაზოთ ეფექტური პროდუქცია კონკურენტულ ფასად.</p>
            <a class="hero-text-link" href="{{ route('catalog') }}">აღმოაჩინე VENTOR <span>↗</span></a>
        </div>
    </div>
</section>
@endsection