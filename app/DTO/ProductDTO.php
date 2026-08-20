<?php

declare(strict_types=1);

namespace App\DTO;

use App\Http\Requests\ProductStoreRequest;
use Spatie\LaravelData\Data;

class ProductDTO extends Data
{
    public function __construct(
        public string $name,
        public float $price,
        public int $stock,
        public string $sku,
        public string $status,
        public ?int $category_id,
        public $image
    ) {
    }

    public function toProductData(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'status' => $this->status,
            'category_id' => $this->category_id,
        ];
    }

    public function fromRequest(ProductStoreRequest $request): self
    {
        //$validated = $request->validated();

        return new self(
            $request->validated('name'),
            $request->validated('price'),
            $request->validated('stock'),
            $request->validated('sku'),
            $request->validated('status'),
            $request->validated('category_id'),
            $request->validated('image'),
        );

    }
}
