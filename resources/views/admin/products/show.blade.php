@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <h1>{{ $product->name }}</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">✏️ Редактировать</a>
                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Удалить товар {{ $product->name }}?')">🗑️ Удалить</button>
                </form>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">← Назад</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded" alt="{{ $product->name }}">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 300px;">
                                <span class="text-muted">Нет изображения</span>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-7">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 150px;">ID</th>
                                <td>{{ $product->id }}</td>
                            </tr>
                            <tr>
                                <th>Название</th>
                                <td>{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <th>Цена</th>
                                <td>{{ number_format($product->price, 0, ',', ' ') }} ₽</td>
                            </tr>
                            <tr>
                                <th>В наличии</th>
                                <td>{{ $product->stock }}</td>
                            </tr>
                            <tr>
                                <th>Категория</th>
                                <td>{{ $product->category?->name ?? 'Без категории' }}</td>
                            </tr>
                            <tr>
                                <th>Дата создания</th>
                                <td>{{ $product->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Описание</th>
                                <td>{{ $product->description ?? '—' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
