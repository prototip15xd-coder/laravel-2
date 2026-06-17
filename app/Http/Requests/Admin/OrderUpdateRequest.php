<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                Order::STATUS_PENDING,
                Order::STATUS_PAID,
                Order::STATUS_SHIPPED,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELED,
            ])],
            'payment_method' => ['required', Rule::in([
                Order::PAYMENT_METHOD_CASH,
                Order::PAYMENT_METHOD_CARD,
            ])],
            'shipping_address' => ['nullable', 'string', 'max:500'],
            'total' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Необходимо указать статус заказа',
            'status.in' => 'Недопустимый статус заказа',
            'payment_method.required' => 'Необходимо указать способ оплаты',
            'payment_method.in' => 'Недопустимый способ оплаты',
            'total.numeric' => 'Сумма должна быть числом',
            'total.min' => 'Сумма не может быть отрицательной',
        ];
    }
}
