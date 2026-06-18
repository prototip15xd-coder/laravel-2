<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\OrderUpdateRequest;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Admin\AdminOrderService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminOrderController
{
    public function index(): Factory|View  // factory?
    {
        $orders = Order::query()
            ->with(['items.product', 'user'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.orders.index', [
            'orders' => $orders,
        ]);
    }

    public function create(): View
    {
        $users = User::all();
        $products = Product::all();

        return view('admin.orders.create', [
            'users' => $users,
            'products' => $products,
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load('items.product')
        ]);
    }

    public function store(OrderStoreRequest $request, AdminOrderService $service): RedirectResponse
    {
        try {
            $data = [
                'user_id' => $request->validated()['user_id'],
                'payment_method' => $request->validated()['payment_method'],
                'status' => 'pending',
                'items' => $request->validated()['items'],
                'total' => $this->calculateTotal($request->validated()['items']),
            ];

            $service->create($data);

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Заказ создан.');

        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->errors());
        }
    }

    private function calculateTotal(array $items): float
    {
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function edit(Order $order): View
    {
        return view('admin.orders.edit', [
            'order' => $order
        ]);
    }

    public function update(OrderUpdateRequest $request, Order $order): RedirectResponse //нет request
    {
        $order->update($request->validated());

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Заказ обновлён');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();
        return redirect()
            ->route('admin.orders.index')
            ->with('success'. 'заказ удален');
    }
}
