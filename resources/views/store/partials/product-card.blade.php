<article class="card product-card">
    <a class="product-card-main" href="{{ route('product.show', ['product' => $product->slug]) }}" aria-label="{{ $product->name }} — დეტალების ნახვა">
        <div class="visual product-card-visual">
            @if($product->hasActiveDiscount())<span class="product-card-sale">-{{ $product->discountPercentage() }}%</span>@endif
            @if($product->image_path)
                <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" loading="lazy" style="width:100%;height:100%;object-fit:contain">
            @else
                <div class="bottle" aria-hidden="true"></div>
            @endif
            <span class="product-card-zoom">↗</span>
        </div>
        <div class="product-card-copy">
            <div class="eyebrow">{{ $product->category?->name ?? 'VENTOR COLLECTION' }}</div>
            <h3>{{ $product->name }}</h3>
            @if($product->short_description)<p class="muted">{{ $product->short_description }}</p>@endif
            <div class="product-card-price">
                @if($product->hasActiveDiscount())<span class="product-card-old-price">{{ number_format((float) $product->compare_at_price, 2) }} ₾</span>@endif
                <strong>{{ number_format((float) $product->price, 2) }} ₾</strong>
            </div>
        </div>
    </a>
    <div class="product-card-actions">
        <a class="btn secondary" href="{{ route('product.show', ['product' => $product->slug]) }}">დეტალები</a>
        @if($product->stock > 0)
            <form method="POST" action="{{ route('cart.add') }}" style="margin:0">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button class="btn" type="submit" aria-label="კალათაში დამატება: {{ $product->name }}">კალათაში +</button>
            </form>
        @else
            <span class="product-card-unavailable">არ არის მარაგში</span>
        @endif
    </div>
</article>