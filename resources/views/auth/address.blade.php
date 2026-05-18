@extends('layouts.app')

@section('title', 'Добавить адрес')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h2 class="h4 mb-0">Новый адрес доставки</h2>
                    </div>

                    <div class="card-body p-4">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('addresses.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="title" class="form-label fw-semibold">Название адреса</label>
                                <input type="text"
                                       class="form-control"
                                       id="title"
                                       name="title"
                                       placeholder="Дом, Работа, Дача..."
                                       value="{{ old('title') }}">
                                <div class="form-text">Необязательно, чтобы легче было ориентироваться</div>
                            </div>

                            <div class="mb-3">
                                <label for="recipient_name" class="form-label fw-semibold">ФИО получателя *</label>
                                <input type="text"
                                       class="form-control @error('recipient_name') is-invalid @enderror"
                                       id="recipient_name"
                                       name="recipient_name"
                                       value="{{ old('recipient_name') }}"
                                       required>
                                @error('recipient_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label fw-semibold">Телефон *</label>
                                <input type="tel"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone') }}"
                                       placeholder="+7 (___) ___-__-__"
                                       required>
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label fw-semibold">Адрес *</label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                          id="address"
                                          name="address"
                                          rows="3"
                                          required>{{ old('address') }}</textarea>
                                @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="city" class="form-label fw-semibold">Город</label>
                                <input type="text"
                                       class="form-control @error('city') is-invalid @enderror"
                                       id="city"
                                       name="city"
                                       value="{{ old('city') }}">
                                @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="postal_code" class="form-label fw-semibold">Почтовый индекс</label>
                                    <input type="text"
                                           class="form-control @error('postal_code') is-invalid @enderror"
                                           id="postal_code"
                                           name="postal_code"
                                           value="{{ old('postal_code') }}">
                                    @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="country" class="form-label fw-semibold">Страна</label>
                                    <input type="text"
                                           class="form-control @error('country') is-invalid @enderror"
                                           id="country"
                                           name="country"
                                           value="{{ old('country', 'Россия') }}">
                                    @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="comment" class="form-label fw-semibold">Комментарий</label>
                                <textarea class="form-control @error('comment') is-invalid @enderror"
                                          id="comment"
                                          name="comment"
                                          rows="2"
                                          placeholder="Домофон, этаж, подъезд...">{{ old('comment') }}</textarea>
                                @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           id="is_default"
                                           name="is_default"
                                           value="1"
                                        {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="is_default">
                                        Сделать основным адресом
                                    </label>
                                    <div class="form-text">Основной адрес будет автоматически подставляться при оформлении заказа</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between gap-2">
                                <a href="{{ route('profile.form') }}" class="btn btn-outline-secondary px-4">
                                    Отмена
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    Сохранить адрес
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
