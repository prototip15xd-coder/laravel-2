<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'title' => ['nullable' , 'string', 'max:100'],
            'recipient_name'  => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'max:20'],
            'address'   => ['required', 'string', 'min:2'],
            'city'   => ['nullable', 'string', 'max:255'],
            'postal_code'   => ['nullable', 'string', 'max:20'],
            'county'   => ['nullable', 'string', 'max:100'],
            'comment'   => ['nullable', 'string'],
            'is_default'   => ['boolean'],
        ];
    }

    /**
     * Возвращает сообщение об ошибке на отдельное взятое правило
     *
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'phone'     => 'Введите номер телефона получателя',
            'address'   => 'Введите адрес на который нужно доставить заказ',
            'city'    => 'Введите город доставки',
            'postal_code'    => 'Введите индекс доставки',
            'county'   => 'Введите страну доставки',
        ];
    }
}
