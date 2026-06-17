@extends('layouts.app')

@section('title', 'Пользователь: ' . $user->name)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Пользователь: {{ $user->name }}</h2>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">Редактировать</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <td>{{ $user->id }}</td>
                            </tr>
                            <tr>
                                <th>Имя</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th>Статус</th>
                                <td>
                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                                    {{ $user->status === 'active' ? 'Активен' : 'Заблокирован' }}
                                </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Роль</th>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-secondary">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th>Дата регистрации</th>
                                <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
