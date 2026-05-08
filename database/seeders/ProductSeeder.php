<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Создать конкретные продукты
        Product::factory()->create([
            'name' => 'iPhone 16 Pro',
            'description' => 'Флагманский смартфон Apple',
            'price' => 120000,
        ]);

        Product::factory()->create([
            'name' => 'MacBook Pro',
            'description' => 'Мощный ноутбук для разработчиков',
            'price' => 250000,
        ]);

        // Создать 50 случайных продуктов
        Product::factory(50)->create();
    }
}
