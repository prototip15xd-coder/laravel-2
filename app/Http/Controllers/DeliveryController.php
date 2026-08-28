<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\DeliveryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DeliveryController
{
    public function __construct(
        private DeliveryService $deliveryService
    ) {
    }
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery_method' => [
                'delivery',
                Rule::in(Order::DELIVERY_METHODS)
            ],
        ];
    }

    public function calculate_delivery($order_id): int
    {
        $user = Auth::user();
        $order = Order::where('id', $order_id)->firstOrFail();
        if (!$order) {
            throw new \Exception('Заказ не найден');
        } else {
            return $this->deliveryService->delivery_total_cost($order->total);
        }
    }
}
