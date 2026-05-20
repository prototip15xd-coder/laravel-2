@extends('layouts.app')

@section('title', 'Редактировать товар')

@section('content')
    <div class="container">
        <h1>Редактировать товар</h1>

        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            @include('admin.products._form', ['product' => $product])

            <button type="submit" class="btn btn-primary">Обновить</button>
        </form>
    </div>
@endsection
