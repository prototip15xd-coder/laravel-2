@extends('layouts.app')

@section('title', 'Управление заказами')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Заказы</h1>
            <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">+ Создать заказ</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Пользователь</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                        <th>Дата</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>
                                {{ $order->user?->name ?? '—' }}
                                <br>
                                <small>{{ $order->user?->email ?? '' }}</small>
                            </td>
                            <td>{{ number_format((float) $order->total, 0, ',', ' ') }} ₽</td>
                            <td>
                                <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'paid' ? 'success' : ($order->status === 'canceled' ? 'danger' : 'secondary')) }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info">Просмотр</a>
                                <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-primary">Редакт</a>
                                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить заказ?')">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Нет заказов</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
