<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\ProductDTO;
use App\DTO\ProductFilterDto;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
//use Illuminate\Contracts\Pagination\LengthAwarePaginator;
//use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    private const array PER_PAGE_OPTIONS = [10, 25, 50, 100];
    /**
     * Подготовка данных для страницы товара.
     * Сейчас здесь минимум логики, но далее мы будем расширять.
     */
    public function getProductPageData(Product $product): Product
    {
        // В будущем тут могут быть:
        // - проверка статуса (active/inactive)
        // - подгрузка связей
        // - вычисление скидок
        // - подготовка DTO / ViewModel

        return $product;
    }

    /**
     * Возвращает список товаров с контролируемой пагинацией.
     */
    public function getProducts(ProductFilterDto $dto): LengthAwarePaginator
    {
        $query = Product::query();

        /**
         * 1) Поиск (базовый)
         * Ищем по name и sku.
         *
         * Важно: оборачиваем OR-условия в where(function),
         * чтобы они не ломали остальные фильтры.
         */
        if ($dto->q) {
            //            $q = $dto->q;
            //            $query->where(function ($subQuery) use ($q) {
            //                $subQuery
            //                    ->where('name', 'like', '%' . $q . '%')
            //                    ->orWhere('sku', 'like', '%' . $q . '%');
            //            });
            return Product::search($dto->q)
                ->paginate($dto->per_page)
                ->withQueryString();
        }

        $query = Product::query();
        /**
         * 2) Фильтр по цене
         */
        if ($dto->min_price !== null) {
            $query->where('price', '>=', $dto->min_price);
        }

        if ($dto->max_price !== null) {
            $query->where('price', '<=', $dto->max_price);
        }

        /**
         * 3) Только в наличии
         */
        if ($dto->in_stock) {
            $query->where('stock', '>', 0);
        }

        /**
         * 4) Сортировка (только через whitelist)
         */
        switch ($dto->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;

            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;

            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;

            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;

            case 'stock_asc':
                $query->orderBy('stock', 'asc');
                break;

            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;

            case 'new':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        /**
         * Вторичная сортировка (чтобы порядок был стабильным, если значения одинаковые)
         * Например: два товара с одинаковой ценой.
         */
        $query->orderByDesc('id');

        /**
         * 5) Пагинация + сохранение query string
         * withQueryString() — чтобы при переключении страниц фильтры НЕ сбрасывались.
         */
        return $query->paginate($dto->per_page)->withQueryString();
    }

    public function getProductsByCategoryId(int $categoryId, ProductFilterDto $dto)
    {
        $query = Product::query()
            ->where('category_id', $categoryId)
            ->with('category');


        return $query
            ->paginate($dto->per_page)
            ->withQueryString();
    }

    public function create(ProductDTO $dto): Product
    {
        $data = $dto->toProductData();

        if ($dto->image) {
            $data['image'] = $dto->image->store('products', 'public');
        }

        return Product::create($data);
    }
    public function update(Product $product, ProductDTO $dto): Product
    {
        $data = $dto->toProductData();

        if ($dto->image) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $dto->image->store('products', 'public');
        }

        $product->update($data);

        return $product;
    }

    public function delete(Product $product): void
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
    }

    public function getCachedProducts(ProductFilterDto $dto, int $page = 1): LengthAwarePaginator
    {
        $key = 'catalog_' . md5(json_encode([
                'page' => $page,
                'q' => $dto->q,
                'min_price' => $dto->min_price,
                'max_price' => $dto->max_price,
                'in_stock' => $dto->in_stock,
                'sort' => $dto->sort,
                'per_page' => $dto->per_page,
            ]));
        //кешируем массив
        $data = Cache::remember($key, 600, function () use ($dto, $page) {
            // Получаем пагинатор
            $result = $this->getProducts($dto);
            $items = $result->items(); // массив объектов

            return [
                // Нужно превратить в массив массивов:
                'data' => array_map(fn ($item) => $item->toArray(), $items),
                'total' => $result->total(),
                'per_page' => $result->perPage(),
                'current_page' => $page,
                'last_page' => $result->lastPage(),
            ];
        });

        $result = new LengthAwarePaginator(
            $data['data'],
            $data['total'],
            $data['per_page'],
            $data['current_page'],
            ['path' => request()->url(), 'query' => request()->query()]
        );
        // возвращаем объект!
        return $result;
    }

    public function getMaxProductPrice(): int
    {
        return (int) Cache::remember('max_product_price', 3600, function () {
            return (int) Product::max('price') ?? 0;
        });
    }

}
