<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\OrderStatusRequest;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\SessionCartService;
use App\Services\YooKassaPaymentService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class OrderController extends Controller
{
    public function index(): RedirectResponse|View  // factory?
    {
        if (!auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('profile.form')
                ->with('error', 'Для просмотра заказов подтвердите email!');
        }

        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->with(['items.product', 'latestPayment'])
            ->orderByDesc('created_at')
            ->get();

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    public function store(
        OrderStoreRequest $request,
        OrderService $service,
        SessionCartService $cart
    ): RedirectResponse {
        $user = Auth::user();

        try {
            \Log::info('1 store OrderController', ['request' => $request]);
            $service->createOrder(
                $user,
                $request->validated()['payment_method'],
                $cart
            );


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

    public function updateStatus(
        Order $order,
        OrderStatusRequest $request,
        OrderService $service
    ): RedirectResponse {
        $order = Order::query()
            ->where('user_id', Auth::id())
            ->whereKey($order->id)
            ->firstOrFail();

        $status = $request->validated()['status'];

        if ($status === Order::STATUS_PAID) {
            $service->markAsPaid($order);
            $message = 'Заказ оплачен.';
        } else {
            $service->cancel($order);
            $message = 'Заказ отменен.';
        }

        return redirect()
            ->route('orders.index')
            ->with('success', $message);
    }

    public function show(Order $order): View
    {
        return view('orders.show', [
            'order' => $order
        ]);
    }

    public function pay(Order $order, YooKassaPaymentService $paymentService): RedirectResponse
    {

        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Проверяем, что заказ НЕ оплачен и НЕ отменён
        if ($order->status !== Order::STATUS_PENDING) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Этот заказ уже оплачен или отменён.');
        }
        \Log::info('оплата 1: pay() payment OrderController', [
            'order_id' => $order->id,
            'order_status' => $order->status,
        ]);

        // Создаём платёж
        $payment = $paymentService->createPaymentForOrder($order);

        \Log::info('оплата 2: pay() payment OrderController', [
            'order_id' => $order->id,
            'order_status' => $order->status,
            'payment' => $payment
        ]);

        // Редиректим на YooKassa
        return redirect()->away($payment->confirmation_url);
    }

}
