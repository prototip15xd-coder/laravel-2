@extends('layouts.app')

@section('title', 'Редактировать пользователя')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Редактировать: {{ $user->name }}</h2>
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

                        <form method="POST" action="{{ route('admin.users.update', $user) }}">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="name" class="form-label">Имя</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Статус</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="active" @selected(old('status', $user->status) === 'active')>Активен</option>
                                    <option value="blocked" @selected(old('status', $user->status) === 'blocked')>Заблокирован</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="role_id" class="form-label">Роль</label>
                                <select name="role_id" id="role_id" class="form-control">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" @selected(old('role_id', $user->roles->first()?->id) == $role->id)>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Отмена</a>
                                <button type="submit" class="btn btn-primary">Сохранить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
