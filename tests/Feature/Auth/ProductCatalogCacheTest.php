<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTO\ProductFilterDto;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ProductCatalogCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_is_cached_after_first_request(): void
    {
        Product::factory()->count(10)->create();

        // Создаём DTO с дефолтными значениями (как в контроллере)
        $dto = new ProductFilterDto(
            q: null,
            min_price: null,
            max_price: null,
            in_stock: false,
            sort: 'new',
            per_page: 10
        );

        $page = 1;

        $result = new ProductService();
        $result->getCachedProducts($dto, $page);

        $key = 'catalog_' . md5(json_encode([
                'page' => $page,
                'q' => $dto->q,
                'min_price' => $dto->min_price,
                'max_price' => $dto->max_price,
                'in_stock' => $dto->in_stock,
                'sort' => $dto->sort,
                'per_page' => $dto->per_page,
            ]));

        $this->get('/products');

        $this->assertTrue(Cache::has($key));
    }
}
