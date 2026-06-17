@extends('layouts.app')

@section('title', 'Заказ #' . $order->id)

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h4 mb-0">Заказ #{{ $order->id }}</h2>
                        <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'paid' ? 'success' : ($order->status === 'canceled' ? 'danger' : 'secondary')) }}">
                        {{ $order->status_label }}
                    </span>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="h6 text-muted mb-2">Пользователь</h5>
                                <p>{{ $order->user?->name ?? '—' }}<br>
                                    <small>{{ $order->user?->email ?? '' }}</small></p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="h6 text-muted mb-2">Дата заказа</h5>
                                <p>{{ $order->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="h6 text-muted mb-2">Способ оплаты</h5>
                            <p>{{ $order->payment_method_label ?? 'Не указан' }}</p>
                        </div>

                        <div class="mb-4">
                            <h5 class="h6 text-muted mb-2">Адрес доставки</h5>
                            <p>{{ $order->shipping_address ?? 'Не указан' }}</p>
                        </div>

                        <h5 class="h6 text-muted mb-3">Товары</h5>
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
                                    <td>{{ $item->product?->name ?? 'Товар удален' }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format((float) $item->price, 0, ',', ' ') }} ₽</td>
                                    <td class="text-end">{{ number_format((float) $item->price * $item->quantity, 0, ',', ' ') }} ₽</td>
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

                        <div class="mt-4">
                            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-primary">Редактировать</a>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">← Назад</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
