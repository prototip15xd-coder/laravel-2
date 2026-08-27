<?php

declare(strict_types=1);

namespace App\Http\Interface;

use App\Models\Product;

interface CartServiceInterface
{
    public function add(Product $product, int $quantity): int;
    public function getItems(): array|null;
    public function clear(): void;
    public function setQuantity(Product $product, int $quantity): int;
    public function getTotalPrice(): string;
    public function getTotalQuantity(): int;
    public function remove(Product $product): void;

}
