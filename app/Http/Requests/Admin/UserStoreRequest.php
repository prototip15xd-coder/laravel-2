<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array     // так а что нам приходит то про пользователя??
    {
        return [
            'role' => ['use', 'admin', 'manager'],
        ];
    }

}
