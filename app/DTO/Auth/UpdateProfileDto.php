<?php

declare(strict_types=1);

namespace App\DTO\Auth;

use App\Http\Requests\UpdateProfileRequest;

class UpdateProfileDto
{
    public function __construct(
        public string $new_password,
    ) {
    }

    public static function fromRequest(UpdateProfileRequest $request): self
    {
        return new self(
            $request->validated('password'),
        );
    }

    //нужно сделать опциональность чтобы не только пароль менялся
    public function toArray(): array
    {
        return [
            'password' => $this->new_password,
            ];
    }

}
