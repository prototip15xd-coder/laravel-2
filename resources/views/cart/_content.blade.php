@if(empty($items))
<div class="alert alert-info mb-0">
    Корзина пуста. <a href="{{ route('categories.index') }}">Перейти в каталог</a>
</div>
@else
<form method="POST"
      action="{{ route('cart.items.update', $product) }}"
      data-ajax-cart="1"
      data-cart-action="update">
    @csrf
    @method('PATCH')
    <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
    <button type="submit" class="btn btn-outline-secondary btn-sm" @disabled($item['quantity'] <= 1)>
    −
    </button>
</form>
<form method="POST"
      action="{{ route('cart.items.update', $product) }}"
      data-ajax-cart="1"
      data-cart-action="set">
    @csrf
    @method('PATCH')
    <input type="number"
           name="quantity"
           value="{{ $item['quantity'] }}"
           min="1"
           max="{{ $product->stock }}"
           step="1"
           class="form-control form-control-sm text-center"
           style="width: 90px">
</form>
<form method="POST"
      action="{{ route('cart.items.update', $product) }}"
      data-ajax-cart="1"
      data-cart-action="update">
    @csrf
    @method('PATCH')
    <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
    <button type="submit" class="btn btn-outline-secondary btn-sm" @disabled($item['quantity'] >= $product->stock)>
    +
    </button>
</form>

@endif

