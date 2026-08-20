@extends('layouts.app')

@section('title', 'Админ-панель')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="row">
            <!-- Боковое меню -->
            <div class="col-md-3 mb-4">
                <div class="list-group">
                    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        📊 Главная
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        👥 Пользователи
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        🛍️ Товары
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        📦 Заказы
                    </a>
                </div>
            </div>

            <!-- Основной контент -->
            <div class="col-md-9">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h2 class="h5 mb-0">Добро пожаловать в админ-панель</h2>
                    </div>
                    <div class="card-body">
                        <p>Вы вошли как: <strong>{{ Auth::user()->name }}</strong></p>
                        <p>Ваша роль:
                            @foreach(Auth::user()->roles as $role)
                                <span class="badge bg-primary">{{ $role->name }}</span>
                            @endforeach
                        </p>

                        <hr>

                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h3 class="display-6">{{ \App\Models\User::count() }}</h3>
                                        <p class="text-muted mb-0">Пользователей</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h3 class="display-6">{{ \App\Models\Product::count() }}</h3>
                                        <p class="text-muted mb-0">Товаров</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h3 class="display-6">{{ \App\Models\Order::count() }}</h3>
                                        <p class="text-muted mb-0">Заказов</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="text-muted">
                                                Последнее обновление:
                                            </div>

                                            <div class="fs-3 fw-bold">
                                                {{ $report['calculated_at'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="text-muted">
                                                Всего заказов
                                            </div>

                                            <div class="fs-3 fw-bold">
                                                {{ $report['orders_count'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="text-muted">
                                                Успешных продаж
                                            </div>

                                            <div class="fs-3 fw-bold">
                                                {{ $report['sales_count'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="text-muted">
                                                Выручка
                                            </div>

                                            <div class="fs-3 fw-bold">
                                                {{ number_format(
                                                    $report['revenue'],
                                                    2,
                                                    ',',
                                                    ' '
                                                ) }} ₽
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="text-muted">
                                                Отменено
                                            </div>

                                            <div class="fs-3 fw-bold">
                                                {{ $report['canceled_count'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped align-middle mt-4">
                                <thead>
                                <tr>
                                    <th>Дата</th>
                                    <th>Заказов</th>
                                    <th>Продаж</th>
                                    <th>Выручка</th>
                                    <th>Средняя выручка за день</th>
                                    <th>Заказов в процессе оформления</th>
                                    <th>Отменено</th>
                                    <th>Пересчитано</th>
                                </tr>
                                </thead>

                                <tbody>
                                @forelse($report['daily_reports'] as $dailyReport)
                                    <tr>
                                        <td>
                                            {{ $dailyReport->report_date->format('d.m.Y') }}
                                        </td>

                                        <td>
                                            {{ $dailyReport->orders_count }}
                                        </td>

                                        <td>
                                            {{ $dailyReport->sales_count }}
                                        </td>

                                        <td>
                                            {{ number_format(
                                                (float) $dailyReport->revenue,
                                                2,
                                                ',',
                                                ' '
                                            ) }} ₽
                                        </td>
                                        <td>
                                            {{ $dailyReport->average_order_value }}
                                        </td>
                                        <td>
                                            {{ $dailyReport->pending_orders_count }}
                                        </td>
                                        <td>
                                            {{ $dailyReport->canceled_count }}
                                        </td>

                                        <td>
                                            {{ $dailyReport->calculated_at?->format('d.m.Y H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            Отчёты ещё не сформированы.
                                            Запустите Scheduler и queue worker.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
