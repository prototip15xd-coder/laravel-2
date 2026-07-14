@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="container py-4">
        <div class="row g-4">

            @include("products.item", ["products" => $product])

            <div class="col-md-7">
                <h1 class="h3 mb-3">{{ $product->name }}</h1>
                <p class="fs-4 fw-semibold mb-3">{{ number_format($product->price, 0, ',', ' ') }} ₽</p>

                <div class="mb-3 text-muted small">
                    <div>Артикул: {{ $product->sku }}</div>
                    <div>В наличии: {{ $product->stock }}</div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold mb-2">Описание</h6>
                    <p class="mb-0">{{ $product->description ?? 'Описание пока не добавлено.' }}</p>
                </div>

                <p>
                    <strong>Категория:</strong>
                    {{ $product->category?->name ?? 'Без категории' }}
                </p>

                <div class="d-flex gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Назад в каталог</a>
                </div>

            </div>
        </div>
    </div>
@endsection
