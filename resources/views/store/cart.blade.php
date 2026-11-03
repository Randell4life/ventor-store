@extends('layouts.store')
@push('head')
<link rel="stylesheet" href="{{ asset('css/storefront-premium.css') }}">
<style>
.cart-layout{display:grid;grid-template-columns:minmax(0,1fr) 330px;gap:24px;align-items:start}
.cart-list{display:grid;gap:12px}
.cart-line{display:grid;grid-template-columns:100px minmax(0,1fr) 110px 110px 42px;gap:16px;align-items:center;padding:16px;background:var(--panel);border:1px solid var(--line)}
.cart-thumb{width:100px;height:100px;background:#0a111c;border:1px solid var(--line);display:grid;place-items:center;overflow:hidden}
.cart-thumb img{width:100%;height:100%;object-fit:contain}
.cart-qty{width:100%;text-align:center}
.cart-summary{position:sticky;top:110px;padding:22px;background:var(--panel);border:1px solid var(--line)}
.cart-summary-row{display:flex;justify-content:space-between;gap:12px;padding:12px 0;border-bottom:1px solid var(--line)}
.cart-remove{width:40px;height:40px;background:transparent;border:1px solid var(--line);color:var(--muted);cursor:pointer}
.cart-remove:hover{color:#ffaaaa;border-color:#8c4545}
.cart-stock{font-size:11px;color:var(--muted);margin-top:5px}
.cart-alert{padding:12px 15px;margin:14px 0;background:#10251e;border:1px solid #285b47;color:#a7e4c2}
.cart-error{padding:12px 15px;margin:14px 0;background:#2a1717;border:1px solid #633838;color:#ffc1c1}
@media(max-width:900px){.cart-layout{grid-template-columns:1fr}.cart-summary{position:relative;top:auto}}
@media(max-width:620px){.cart-line{grid-template-columns:76px minmax(0,1fr) 42px;gap:11px;padding:11px}.cart-thumb{width:76px;height:76px}.cart-quantity{grid-column:2}.cart-line-total{grid-column:2}.cart-remove{grid-column:3;grid-row:1}.cart-qty{max-width:100px}.cart-summary{padding:17px}}
</style>
@endpush
@section('title','კალათა — VENTOR')
@section('content')
<section class="section">
    <div class="wrap">
        <div class="eyebrow">YOUR CART</div>
        <h1 style="font-size:clamp(34px,5vw,52px)">შენი კალათა</h1>
        <p class="muted">შეამოწმე პროდუქტები და რაოდენობები შეკვეთის დადასტურებამდე.</p>

        @if(session('success'))<div class="cart-alert">{{ session('success') }}</div>@endif
        @if(session('cart_adjusted'))<div class="cart-alert">{{ session('cart_adjusted') }}</div>@endif
        @if($errors->any())<div class="cart-error">{{ $errors->first() }}</div>@endif

        @if($lines->isEmpty())
            <div class="card" style="text-align:center;padding:45px 20px;margin-top:25px">
                <div class="eyebrow">CART IS EMPTY</div>
                <h2>კალათა ჯერ ცარიელია</h2>
                <p class="muted">დაათვალიერე კატალოგი და დაამატე სასურველი პროდუქტები.</p>
                <a class="btn" href="{{ route('catalog') }}">კატალოგის ნახვა</a>
            </div>
        @else
            <div class="cart-meta" style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;margin:22px 0 14px;color:var(--muted);font-size:12px">
                <span>{{ $itemCount }} ცალი პროდუქტი</span>
                <a href="{{ route('catalog') }}">+ სხვა პროდუქტების დამატება</a>
            </div>

            <div class="cart-layout">
                <div>
                    <form id="cart-update" method="POST" action="{{ route('cart.update') }}">
                        @csrf @method('PATCH')
                        <div class="cart-list">
                            @foreach($lines as $line)
                                <article class="cart-line">
                                    <a class="cart-thumb" href="{{ route('product.show',['product'=>$line['product']->slug]) }}">
                                        @if($line['product']->image_path)
                                            <img src="{{ asset('storage/'.$line['product']->image_path) }}" alt="{{ $line['product']->name }}" loading="lazy">
                                        @else
                                            <span class="bottle" aria-hidden="true"></span>
                                        @endif
                                    </a>
                                    <div>
                                        <a href="{{ route('product.show',$line['product']) }}"><strong>{{ $line['product']->name }}</strong></a>
                                        @if($line['product']->brand)<p class="muted" style="margin:5px 0">{{ $line['product']->brand }}</p>@endif
                                        <div class="cart-stock">მარაგშია: {{ $line['stock'] }} ცალი</div>
                                        <div class="cart-stock">{{ number_format((float)$line['product']->price,2) }} ₾ / ცალი</div>
                                    </div>
                                    <div class="cart-quantity">
                                        <label class="cart-stock" for="quantity-{{ $line['product']->id }}">რაოდენობა</label>
                                        <input id="quantity-{{ $line['product']->id }}" class="field cart-qty" type="number"
                                            name="quantities[{{ $line['product']->id }}]" value="{{ $line['quantity'] }}"
                                            min="1" max="{{ min(30,$line['stock']) }}" form="cart-update" required>
                                    </div>
                                    <strong class="cart-line-total">{{ number_format($line['line_total'],2) }} ₾</strong>
                                    <button class="cart-remove" type="submit" form="remove-{{ $line['product']->id }}" aria-label="პროდუქტის წაშლა" title="წაშლა">×</button>
                                </article>
                            @endforeach
                        </div>
                        <button class="btn secondary" type="submit" style="margin-top:15px">რაოდენობების განახლება</button>
                    </form>

                    @foreach($lines as $line)
                        <form id="remove-{{ $line['product']->id }}" method="POST" action="{{ route('cart.remove',$line['product']) }}">
                            @csrf @method('DELETE')
                        </form>
                    @endforeach
                </div>

                <aside class="cart-summary">
                    <div class="eyebrow">ORDER SUMMARY</div>
                    <h2 style="margin:8px 0 15px">შეკვეთის შეჯამება</h2>
                    <div class="cart-summary-row"><span class="muted">პროდუქტები</span><strong>{{ $itemCount }} ცალი</strong></div>
                    <div class="cart-summary-row"><span class="muted">ქვეჯამი</span><strong>{{ number_format($total,2) }} ₾</strong></div>
                    <div class="cart-summary-row"><span class="muted">მიწოდება</span><span class="muted">დადასტურდება შეკვეთისას</span></div>
                    <div style="display:flex;justify-content:space-between;gap:12px;padding:20px 0;font-size:20px"><strong>ჯამი</strong><strong>{{ number_format($total,2) }} ₾</strong></div>
                    <a class="btn" style="display:block;text-align:center;width:100%" href="{{ route('checkout') }}">შეკვეთის გაფორმება →</a>
                    <p class="muted" style="font-size:11px;line-height:1.7;margin:14px 0 0">საბოლოო ხელმისაწვდომობა ხელახლა მოწმდება შეკვეთის დადასტურებისას.</p>
                </aside>
            </div>
        @endif
    </div>
</section>
@endsection
