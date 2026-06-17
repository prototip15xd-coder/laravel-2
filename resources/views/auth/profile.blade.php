@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
    <div class="container-fluid px-2 px-sm-3 py-3 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-11 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3 p-sm-4 p-md-5">
                        <h2 class="text-center mb-3 mb-md-4 h3 h2-md">Профиль пользователя</h2>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Кнопка перехода в админку (только для админов и менеджеров) -->
                        @auth
                            @if(Auth::user()->hasAnyRole(['admin', 'manager']))
                                <div class="d-grid gap-2 mb-4">
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-danger py-2">
                                        ⚡ Перейти в режим админа
                                    </a>
                                </div>
                            @endif
                        @endauth

                        <form method="POST" action="{{ route('profile.update', $user) }}">
                            @csrf
                            @method('PATCH')

                            <div class="row g-2 g-md-3">
                                <div class="col-12 col-md-6">
                                    <div class="mb-2 mb-md-3">
                                        <label for="first_name" class="form-label small">Имя</label>
                                        <input type="text"
                                               name="first_name"
                                               id="first_name"
                                               class="form-control @error('first_name') is-invalid @enderror"
                                               value="{{ old('first_name', $user->first_name) }}">
                                        @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="mb-2 mb-md-3">
                                        <label for="last_name" class="form-label small">Фамилия</label>
                                        <input type="text"
                                               name="last_name"
                                               id="last_name"
                                               class="form-control @error('last_name') is-invalid @enderror"
                                               value="{{ old('last_name', $user->last_name) }}">
                                        @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2 mb-md-3">
                                <label for="email" class="form-label small">Email</label>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 mb-md-4">
                                <label for="phone" class="form-label small">Телефон</label>
                                <input type="tel"
                                       name="phone"
                                       id="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $user->phone) }}"
                                       placeholder="+7 (___) ___-__-__">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary py-2">
                                    Сохранить изменения
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="d-grid gap-2">
                            <a href="{{ route('password.form') }}" class="btn btn-outline-secondary py-2">
                                Изменить пароль
                            </a>
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-primary py-2">
                                📦 Мои заказы
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Адреса доставки -->
                <div class="card shadow-sm border-0 mt-3">
                    <div class="card-body p-3 p-sm-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="h5 mb-0">Адреса доставки</h3>
                            <a href="{{ route('addresses.create') }}" class="btn btn-sm btn-outline-primary">
                                + Добавить адрес
                            </a>
                        </div>

                        @if($addresses->isEmpty())
                            <div class="alert alert-light text-center py-3">
                                <p class="mb-0 text-muted">Нет сохранённых адресов</p>
                            </div>
                        @else
                            @foreach($addresses as $address)
                                <div class="border rounded p-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            @if($address->is_default)
                                                <span class="badge bg-primary mb-1">Основной</span>
                                            @endif
                                            @if($address->title)
                                                <strong class="d-block">{{ $address->title }}</strong>
                                            @endif
                                            <div class="small">{{ $address->recipient_name }}</div>
                                            <div class="small">{{ $address->phone }}</div>
                                            <div class="small">{{ $address->address }}</div>
                                            @if($address->comment)
                                                <div class="small text-muted">{{ $address->comment }}</div>
                                            @endif
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            @if(!$address->is_default)
                                                <form method="POST" action="{{ route('addresses.set-default', $address) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-primary">Основной</button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('addresses.destroy', $address) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Удалить адрес?')">Удалить</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const phoneInput = document.querySelector('input[name="phone"]');
            if (phoneInput.value) {
                phoneInput.value = phoneInput.value.replace(/\+/g, '');
            }
        });
    </script>
@endpush
