@extends('layouts.app')

@section('title', 'Создать товар')

@section('content')
    <div class="container">
        <h1>Создать товар</h1>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf

            @include('admin.products._form', ['product' => null])

            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
@endsection
