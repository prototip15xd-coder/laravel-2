@if(empty($items))
    <div class="alert alert-info mb-0">
        Корзина пуста. <a href="{{ route('products.index') }}">Перейти в каталог</a>
    </div>
@else
    @foreach($items as $item)
        @php($product = $item['product'])
        <div class="card mb-2">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="mb-0">{{ $product->name }}</h6>
                        <small class="text-muted">{{ number_format($item['unit_price'], 0, ',', ' ') }} ₽</small>
                        <div class="small text-muted">Всего: {{ number_format($item['subtotal'], 0, ',', ' ') }} ₽</div>
                    </div>

                    <div class="col-auto">
                        <div class="d-flex align-items-center gap-2">
                            <!-- Минус -->
                            <form method="POST"
                                  action="{{ route('cart.items.update', $product) }}"
                                  data-ajax-cart="1"
                                  data-cart-action="update"
                                  class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                <button type="submit" class="btn btn-outline-secondary btn-sm" {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>−</button>
                            </form>

                            <!-- Количество -->
                            <form method="POST"
                                  action="{{ route('cart.items.update', $product) }}"
{{--                                  data-ajax-cart="1"--}}
                                  data-cart-action="set"
                                  class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="number"
                                       name="quantity"
                                       value="{{ $item['quantity'] }}"
                                       min="1"
                                       max="{{ $product->stock }}"
                                       step="1"
                                       class="form-control form-control-sm text-center"
                                       style="width: 80px">
                            </form>

                            <!-- Плюс -->
                            <form method="POST"
                                  action="{{ route('cart.items.update', $product) }}"
                                  data-ajax-cart="1"
                                  data-cart-action="update"
                                  class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                <button type="submit" class="btn btn-outline-secondary btn-sm" {{ $item['quantity'] >= $product->stock ? 'disabled' : '' }}>+</button>
                            </form>

                            <!-- Удалить -->
                            <form method="POST"
                                  action="{{ route('cart.items.destroy', $product) }}"
                                  data-ajax-cart="1"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">🗑</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="mt-3 pt-2 border-top">
        <strong>Итого: {{ $totalQuantity }} шт. на сумму {{ number_format($totalPrice, 0, ',', ' ') }} ₽</strong>
    </div>

    <div class="mt-3">
        <form method="POST"
              action="{{ route('cart.clear') }}"
              data-ajax-cart="1"
              class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">Очистить корзину</button>
        </form>
    </div>
@endif
