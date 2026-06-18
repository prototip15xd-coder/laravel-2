<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\ProductDTO;
use App\DTO\ProductFilterDto;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Models\Product;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {
    }

    public function index(ProductFilterRequest $request)
    {
        // 1) Из validated данных собираем DTO
        $dto = ProductFilterDto::fromRequest($request);

        // 2) Сервис строит запрос
        $products = $this->productService->getProducts($dto);

        // 3) Для подсказки диапазона цен (placeholder)
        $maxProductPrice  = $this->productService->getMaxProductPrice();

        return view('products.index', compact('products', 'dto', 'maxProductPrice'));
    }

    public function show(Product $product)
    {
        // Страница товара — отдельный урок, здесь оставляем как есть
        return view('products.show', compact('product'));
    }

    public function store(ProductStoreRequest $request, ProductService $service)
    {
        $dto = ProductDTO::fromRequest($request);

        $service->create($dto);

        return redirect()->route('admin.products.index');
    }
}
