<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class AdminOrderService
{
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {

            $order = Order::create([
                'user_id' => $data['user_id'],
                'status'  => $data['status'],
            ]);

            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);
            }

            return $order;
        });
    }


}
