<div class="col">
    <div class="card h-100">
        @if($product->image)
            <div>
                <a href="{{ route("products.show", $product) }}">
                    <img src="storage/{{$product->image }}"
                         class="card-img-top"
                         alt="{{ $product->name }}">
                </a>
            </div>
        @else
            <div class="card-img-top d-flex align-items-center justify-content-center bg-light"
                 style="height: 180px;">
                <a href="{{ route("products.show", $product) }}">
                    <span class="text-muted">Нет изображения</span>
                </a>
            </div>
        @endif
        <div class="text-muted">
            Категория: {{ $product->category?->name ?? 'Без категории' }}
        </div>

        <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $product->name }}</h5>
            <p class="fw-semibold mb-3">{{ number_format($product->price, 0, ',', ' ') }} ₽</p>

            <form method="POST"
                  action="{{ route('cart.items.store', $product) }}"
                  class="d-inline mt-2">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-primary w-100">В корзину</button>
            </form>
        </div>
    </div>
</div>
