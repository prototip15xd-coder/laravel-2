@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
    <div class="container-fluid px-2 px-sm-3 py-3 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-11 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3 p-sm-4 p-md-5">
                        <h2 class="text-center mb-3 mb-md-4 h3 h2-md">Изменение пароля пользователя</h2>

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('PATCH')

                            <div class="row g-2 g-md-3">
                                <div class="col-12 col-md-6">
                                    <div class="mb-2 mb-md-3">
                                        <label for="current_password" class="form-label small">Текущий пароль</label>
                                        <input type="text"
                                               name="current_password"
                                               id="current__password"
                                               class="form-control @error('current_password') is-invalid @enderror">
                                        @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2 g-md-3">
                                <div class="col-12 col-md-6">
                                    <div class="mb-2 mb-md-3">
                                        <label for="new_password" class="form-label small">Новый пароль</label>
                                        <input type="text"
                                               name="new_password"
                                               id="new_password"
                                               class="form-control @error('new_password') is-invalid @enderror">
                                        @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2 g-md-3">
                                <div class="col-12 col-md-6">
                                    <div class="mb-2 mb-md-3">
                                        <label for="new_password" class="form-label small">Повторите пароль</label>
                                        <input type="text"
                                               name="new_password"
                                               id="new_password"
                                               class="form-control @error('new_password') is-invalid @enderror">
                                        @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary py-2">
                                    Сохранить изменения
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">
                    </div>
                </div>


                <!-- Удаление профиля -->
                @auth
                    <div class="card shadow-sm border-0 mt-3">
                        <div class="card-body p-3 p-sm-4">
                            <h3 class="h5 mb-3 text-danger">⚠️ Опасная зона</h3>
                            <p class="small text-muted">Удаление профиля необратимо. Все данные будут удалены.</p>

                            <form method="POST" action="{{ route('profile.destroy') }}"
                                  onsubmit="return confirm('Вы уверены, что хотите удалить профиль? Это необратимо!');">
                                @csrf
                                @method('DELETE')

                                <div class="mb-3">
                                    <label for="delete_password" class="form-label small">Введите пароль для подтверждения</label>
                                    <input type="password"
                                           name="password"
                                           id="delete_password"
                                           class="form-control"
                                           required
                                           placeholder="Введите ваш пароль">
                                </div>

                                <button type="submit" class="btn btn-danger w-100 py-2">
                                    🗑️ Удалить профиль
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
@endsection


