@extends('layouts.app')

@section('title', 'Управление пользователями')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Пользователи</h1>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Создать пользователя</a>
        </div>
        @auth
            @if(Auth::user()->hasAnyRole(['admin', 'manager']))
                <div class="d-grid gap-2 mb-4">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-danger py-2">
                        Вернуться на панель админа
                    </a>
                </div>
            @endif
        @endauth

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Роли</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-secondary">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                <!-- Кнопка редактирования -->
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                    ✏️ Редактировать
                                </a>

                                <!-- Кнопка просмотра -->
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info">
                                    👁️ Просмотр
                                </a>

                                <!-- Форма удаления -->
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить пользователя {{ $user->name }}?')">
                                        🗑️ Удалить
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
