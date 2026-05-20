<?php

declare(strict_types=1);

namespace App\DTO;

use App\Http\Requests\ProductListRequest;
use Spatie\LaravelData\Data;

class ProductListDto extends Data
{
    public function __construct(
        public int $per_page,
    ) {
    }

    public static function fromRequest(ProductListRequest $request): self
    {
        return new self(
            per_page: $request->validated('per_page') ?? 10
        );
    }
}
