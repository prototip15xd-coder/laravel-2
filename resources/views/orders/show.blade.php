@extends('layouts.app')

@section('title', 'Заказ #' . $order->id)

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h4 mb-0">Заказ #{{ $order->id }}</h2>
                        <span class="badge bg-secondary">{{ $order->status }}</span>
                    </div>

                    <div class="card-body">
                        <!-- Информация о заказе -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="h6 text-muted mb-2">Дата заказа</h5>
                                <p>{{ $order->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="h6 text-muted mb-2">Способ оплаты</h5>
                                <p>{{ $order->payment_method === 'cash' ? 'Наличными' : 'Картой' }}</p>
                            </div>
                        </div>

                        <!-- Адрес доставки -->
                        <div class="mb-4">
                            <h5 class="h6 text-muted mb-2">Адрес доставки</h5>
                            <p>{{ $order->shipping_address ?? 'Не указан' }}</p>
                        </div>

                        <!-- Товары в заказе -->
                        <h5 class="h6 text-muted mb-3">Товары</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                <tr>
                                    <th>Товар</th>
                                    <th class="text-center">Кол-во</th>
                                    <th class="text-end">Цена</th>
                                    <th class="text-end">Сумма</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            {{ $item->product?->name ?? 'Товар удален' }}
                                            @if(!$item->product)
                                                <span class="badge bg-danger">Удален</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">{{ number_format($item->price, 0, ',', ' ') }} ₽</td>
                                        <td class="text-end">{{ number_format($item->price * $item->quantity, 0, ',', ' ') }} ₽</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Итого:</th>
                                    <th class="text-end">{{ number_format((float) $order->total, 0, ',', ' ') }} ₽</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Кнопки действий (только для pending заказов) -->
                        @if($order->status === 'pending')
                            <div class="d-flex gap-2 mt-4">
                                <form method="POST" action="{{ route('orders.status.update', $order) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="paid">
                                    <button type="submit" class="btn btn-success">✅ Оплатить</button>
                                </form>

                                <form method="POST" action="{{ route('orders.status.update', $order) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="canceled">
                                    <button type="submit" class="btn btn-danger">❌ Отменить</button>
                                </form>
                            </div>
                        @endif

                        <!-- Кнопка назад -->
                        <div class="mt-4">
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">← Назад к списку заказов</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
