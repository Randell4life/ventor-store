<article class="card product-card">
    <a href="{{ route('product.show', $product) }}" aria-label="{{ $product->name }} — დეტალების ნახვა">
        <div class="visual">
            @if($product->image_path)
                <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" loading="lazy" style="width:100%;height:100%;object-fit:contain">
            @else
                <div class="bottle" aria-hidden="true"></div>
            @endif
        </div>
        <div class="eyebrow">{{ $product->category?->name ?? 'VENTOR' }}</div>
        <h3 style="font-size:15px;min-height:42px">{{ $product->name }}</h3>
        @if($product->short_description)
            <p class="muted" style="font-size:12px">{{ $product->short_description }}</p>
        @endif
        <div class="price">
            @if($product->compare_at_price && $product->compare_at_price > $product->price)
                <span style="text-decoration:line-through;color:var(--muted);font-size:13px">{{ number_format((float) $product->compare_at_price, 2) }} ₾</span>
            @endif
            {{ number_format((float) $product->price, 2) }} ₾
        </div>
    </a>
    <div style="display:flex;gap:8px;align-items:center">
        <a class="btn secondary" style="flex:1" href="{{ route('product.show', $product) }}">დეტალები</a>
        @if($product->stock > 0)
            <form method="POST" action="{{ route('cart.add') }}" style="margin:0">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button class="btn" type="submit" aria-label="კალათაში დამატება: {{ $product->name }}">+</button>
            </form>
        @else
            <span class="muted" style="font-size:11px">არ არის მარაგში</span>
        @endif
    </div>
</article>
