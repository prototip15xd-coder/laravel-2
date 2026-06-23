@extends('layouts.app')

@section('title', 'Подтвердите email')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Подтвердите email</h2>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <p>На ваш email отправлена ссылка для подтверждения.</p>
                        <p>Перейдите по ней, чтобы активировать аккаунт.</p>

                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Отправить повторно
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
