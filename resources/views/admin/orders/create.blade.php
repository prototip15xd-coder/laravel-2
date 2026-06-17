@extends('layouts.app')

@section('title', 'Создать заказ')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Создать заказ</h2>
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

                        <form method="POST" action="{{ route('admin.orders.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Способ оплаты</label>
                                <select name="payment_method" id="payment_method" class="form-control">
                                    <option value="cash" @selected(old('payment_method') === 'cash')>Наличными</option>
                                    <option value="card" @selected(old('payment_method') === 'card')>Картой</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Отмена</a>
                                <button type="submit" class="btn btn-primary">Создать</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
