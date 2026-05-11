<?php

declare(strict_types=1);

namespace App\DTO;

use App\Http\Requests\CartRequest;
use Spatie\LaravelData\Data;

class CartDTO extends Data
{
    public function __construct(
        public readonly int $quantity,
    ) {
    }

    public static function fromRequest(CartRequest $request): self
    {
        $validated = $request->validated();

        // quantity может быть null только для update (при удалении)
        $quantity = (int) ($validated['quantity'] ?? 1);

        $quantity = max(1, min(999, $quantity));

        return new self(
            quantity: $quantity,
        );
    }

    // Дополнительный метод для update (где quantity может быть 0)
    public static function fromRequestForUpdate(CartRequest $request): self
    {
        $validated = $request->validated();

        $quantity = isset($validated['quantity'])
            ? (int) $validated['quantity']
            : 1;

        $quantity = min(999, max(0, $quantity));

        return new self(
            quantity: $quantity,
        );
    }
}
