@extends('layouts.app')

@section('title', 'Админ-панель')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="list-group">
                    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action active">
                        📊 Главная
                    </a>
                    <!-- Убрал admin.users, admin.roles, admin.orders -->
                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Добро пожаловать в админ-панель</h2>
                    </div>
                    <div class="card-body">
                        <p>Вы вошли как: <strong>{{ Auth::user()->name }}</strong></p>
                        <p>Ваша роль:
                            @foreach(Auth::user()->roles as $role)
                                <span class="badge bg-primary">{{ $role->name }}</span>
                            @endforeach
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
