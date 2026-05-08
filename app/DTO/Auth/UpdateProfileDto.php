<?php

declare(strict_types=1);

namespace App\DTO\Auth;

use App\Http\Requests\PasswordUpdateRequest;

class UpdateProfileDto
{
    public function __construct(
        public string $new_password,
    ) {
    }

    public static function fromRequest(PasswordUpdateRequest $request): self
    {
        return new self(
            $request->validated('password'),
        );
    }

}
