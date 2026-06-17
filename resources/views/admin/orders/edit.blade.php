@extends('layouts.app')

@section('title', 'Редактировать заказ #' . $order->id)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Редактировать заказ #{{ $order->id }}</h2>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="status" class="form-label">Статус</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="pending" @selected(old('status', $order->status) === 'pending')>Ожидает оплаты</option>
                                    <option value="paid" @selected(old('status', $order->status) === 'paid')>Оплачен</option>
                                    <option value="shipped" @selected(old('status', $order->status) === 'shipped')>Отправлен</option>
                                    <option value="completed" @selected(old('status', $order->status) === 'completed')>Завершен</option>
                                    <option value="canceled" @selected(old('status', $order->status) === 'canceled')>Отменен</option>
                                </select>
                                @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Способ оплаты</label>
                                <select name="payment_method" id="payment_method" class="form-control">
                                    <option value="cash" @selected(old('payment_method', $order->payment_method) === 'cash')>Наличными</option>
                                    <option value="card" @selected(old('payment_method', $order->payment_method) === 'card')>Картой</option>
                                </select>
                                @error('payment_method') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="shipping_address" class="form-label">Адрес доставки</label>
                                <textarea name="shipping_address" id="shipping_address" class="form-control @error('shipping_address') is-invalid @enderror" rows="2">{{ old('shipping_address', $order->shipping_address) }}</textarea>
                                @error('shipping_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="total" class="form-label">Сумма</label>
                                <input type="number" step="0.01" name="total" id="total" class="form-control @error('total') is-invalid @enderror" value="{{ old('total', $order->total) }}">
                                @error('total') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Отмена</a>
                                <button type="submit" class="btn btn-primary">Сохранить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
