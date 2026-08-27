<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_catalog_page_is_available(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_catalog_contains_heading(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Каталог товаров');
    }
}
