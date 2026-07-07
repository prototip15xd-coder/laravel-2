<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Services\OrderService;
use App\Services\YooKassaPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class YooKassaController extends Controller
{
    public function index()
    {
    } ///нужен??

    public function return(
        OrderService $orderService,
        Order $order,
        OrderPayment $orderPayment,
        YooKassaPaymentService $yooKassaPaymentService
    ): RedirectResponse {
        $yooKassaPaymentService->fetchPayment($orderPayment);
        $orderPayment->refresh();

        if ($orderPayment->status === 'succeeded') {
            $orderService->markAsPaid($order);
            return redirect()->route('orders.show', $order)
                ->with('sucsess', 'Оплата прошла успешно!');
        }
        return redirect()->route('orders.show', $order)
            ->with('error', 'Оплата не прошла. Попробуйте позже');

    }

    public function webhook(
        YooKassaRequest $request,
        YooKassaPaymentService $yooKassaPaymentService
    ): JsonResponse {
        $weebhookData = $request->all();
        $yooKassaPaymentService->handleWebhook($weebhookData);

        return response()->json(['status' => 'success']);
    }

}
