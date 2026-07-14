@extends('layouts.app')

@section('title', 'Создать товар')

@section('content')
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h2 class="h5 mb-0">Создать товар</h2>
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

                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Название</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Цена</label>
                        <input type="number" name="price" class="form-control" value="{{ old('price') }}" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">В наличии</label>
                        <input type="number" name="stock" class="form-control" value="{{ old('stock') }}" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Категория</label>
                        <select name="category_id" class="form-control">
                            <option value="">Без категории</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Изображение</label>
                        <input type="file" name="image" class="form-control">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Отмена</a>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
