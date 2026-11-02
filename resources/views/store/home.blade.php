@extends('layouts.store')
@push('head')<link rel="stylesheet" href="{{ asset('css/storefront-premium.css') }}">@endpush
@section('title', 'VENTOR — პრემიუმ მოვლა ავტომობილისა და მოტოციკლისთვის')
@section('content')
<section class="hero">
    <div class="wrap">
        <div class="eyebrow">PREMIUM AUTOMOTIVE CARE / GEORGIA</div>
        <h1>ზრუნვა, რომელიც<br><span style="color:var(--ice)">ყველა დეტალში</span> იგრძნობა.</h1>
        <p>ავტომობილისა და მოტოციკლის მოვლის გააზრებული არჩევანი მათთვის, ვინც ხარისხს კომპრომისის გარეშე ირჩევს.</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:28px">
            <a class="btn" href="{{ route('catalog') }}">პროდუქციის ნახვა <span aria-hidden="true">↗</span></a>
            <a class="btn secondary" href="#about">VENTOR-ის შესახებ</a>
        </div>
        <div class="muted" style="display:flex;gap:22px;flex-wrap:wrap;margin-top:32px;font-size:11px;letter-spacing:.08em">
            <span>✦ შერჩეული მოვლის საშუალებები</span><span>✦ გამჭვირვალე ინფორმაცია</span><span>✦ ხარისხი ყველა დეტალში</span>
        </div>
    </div>
</section>
<section class="section">
    <div class="wrap">
        <div class="section-head">
            <div><div class="eyebrow">01 / EXPLORE</div><h2>აირჩიე მიმართულება</h2></div>
            <p class="muted" style="max-width:420px;line-height:1.8">იპოვე შენთვის საჭირო მოვლის საშუალებები კატეგორიების მიხედვით და დაათვალიერე ხელმისაწვდომი პროდუქცია.</p>
        </div>
        <div class="grid">
            @forelse($categories as $category)
                <a class="category" href="{{ route('catalog', ['category' => $category->slug]) }}">
                    <div class="eyebrow">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }} / VENTOR</div>
                    <h3 style="margin:20px 0 8px">{{ $category->name }}</h3>
                    @if($category->description)<p class="muted" style="font-size:12px;line-height:1.7">{{ $category->description }}</p>@endif
                    <span style="color:var(--ice)">კატეგორიის ნახვა ↗</span>
                </a>
            @empty
                <p class="muted">კატეგორიები მალე დაემატება.</p>
            @endforelse
        </div>
    </div>
</section>
<section class="section" style="background:#0b111a;border-block:1px solid var(--line)">
    <div class="wrap">
        <div class="section-head">
            <div><div class="eyebrow">02 / SELECTED COLLECTION</div><h2>რჩეული პროდუქცია</h2></div>
            <a href="{{ route('catalog') }}" class="muted">მთელი კატალოგი ↗</a>
        </div>
        <div class="grid">
            @forelse($featured as $product)
                @include('store.partials.product-card', ['product' => $product])
            @empty
                <div class="card" style="grid-column:1/-1;padding:28px">
                    <h3>რჩეული პროდუქცია მალე გამოჩნდება</h3>
                    <p class="muted">ადმინისტრაციიდან მონიშნე აქტიური პროდუქტი როგორც რჩეული, რომ ის მთავარ გვერდზე გამოჩნდეს.</p>
                    <a class="btn secondary" href="{{ route('catalog') }}">კატალოგის ნახვა</a>
                </div>
            @endforelse
        </div>
    </div>
</section>
<section class="section" id="about">
    <div class="wrap product-layout">
        <div>
            <div class="eyebrow">03 / THE VENTOR STANDARD</div>
            <h2>ხარისხი ყველა დეტალში.</h2>
        </div>
        <div>
            <p class="muted" style="line-height:2">VENTOR აერთიანებს ავტომობილისა და მოტოციკლის მოვლის პროდუქტებს. ჩვენი მიზანია მომხმარებელს შევთავაზოთ გააზრებული არჩევანი, ზუსტი ინფორმაცია და ყურადღებიანი მომსახურება.</p>
            <p class="muted" style="line-height:2">პროდუქტის ფასი და ხელმისაწვდომობა აისახება კატალოგში მხოლოდ მას შემდეგ, რაც ინფორმაცია ადმინისტრაციაში დადასტურდება.</p>
            <a class="btn" href="{{ route('catalog') }}">აღმოაჩინე კატალოგი ↗</a>
        </div>
    </div>
</section>
@endsection
