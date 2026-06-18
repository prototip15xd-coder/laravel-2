<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'integer'],
            'status' => ['required', 'in:active,inactive'],
            'image' => ['nullable', 'image', 'max:2048'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ];
    }

    public function fromRequest(FormRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            $request->validated('name'),
            $request->validated('price'),
            $request->validated('stock'),
            $request->validated('status'),
            $request->validated('category_id'),
        );
    }
}
