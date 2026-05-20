<?php

declare(strict_types=1);

namespace App\DTO;

use App\Http\Requests\RoleRequest;
use Spatie\LaravelData\Data;

class RoleDTO extends Data
{
    public function __construct(
        public string $name,
        public string $slug,
    ) {
    }

    public static function fromRequest(RoleRequest $request): self
    {
        $validated = $request->validated();
        return new self(
            name: $request->validated('name'),
            slug: $request->validated('slug') ?? 'user',
        );
    }
}
