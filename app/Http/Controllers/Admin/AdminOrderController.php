<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\OrderUpdateRequest;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Services\Admin\AdminOrderService;
use App\Services\SessionCartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PHPUnit\Event\Code\Throwable;

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
    public function store(OrderStoreRequest $request, AdminOrderService $service)
    {
        $service->create($request->validated());

        return redirect()->route('admin.orders.index');
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load('items.product')
        ]);
    }

    public function create(
        OrderStoreRequest $request,
        AdminOrderService $service,
        SessionCartService $cart
    ): RedirectResponse {
        try {
            $data = [
                'user_id' => Auth::id(),
                'status' => 'pending',
                'payment_method' => $request->validated()['payment_method'],
                'items' => $cart->getItems(),
                'total' => $cart->getTotalPrice()
            ];

            $service->create($data);
            return redirect()
                ->route('orders.index')
                ->with('success', 'Заказ создан.');
        } catch (ValidationException $e) {
            // Ошибки валидации (пустая корзина, нет адреса)
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->errors());

        } catch (Throwable $e) {
            // Все остальные ошибки
            report($e); // записать в лог

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Произошла ошибка при оформлении заказа. Попробуйте позже.']);
        }
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
