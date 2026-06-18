<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\Admin\AdminProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with('category')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.products.index', [
            'products' => $products,
        ]);
    }

    public function show(Product $product): View
    {
        return view('admin.products.show', [
            'product' => $product->load('category'),
        ]);
    }

    public function store(
        ProductStoreRequest $request,
        AdminProductService $service,
    ): RedirectResponse {
        try {
            $data = [
                'name' => $request['name'],
                'description'  => $request['description'],
                'price'      => $request['price'],
                'image'     => $request['image'],
                'category_id'    => $request['category_id'],
                'stock'   => $request['stock'],
            ];
            $service->create($data);
            return redirect()
                ->route('products.index')
                ->with('success', 'Продукт создан.');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->errors());

        }
    }

    public function create(): View
    {
        $categories = Category::query()
            ->orderByDesc('created_at');
        return view('admin.products.create', [
            'categories' => $categories,
        ]);
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse //нет request
    {
        $product->update($request->validated());

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Товар обновлён');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        return redirect()
            ->route('admin.products.index')
            ->with('success'. 'товар удален');
    }
}
