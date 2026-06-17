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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
