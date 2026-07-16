<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Services\OrderService;
use App\Services\YooKassaPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
        Request $request,  //YooKassaRequest
        YooKassaPaymentService $yooKassaService,
        OrderService $orderService
    ): JsonResponse {
        $weebhookData = $request->all();
        $yooKassaService->handleWebhook($weebhookData);

        $paymentId = $request->input('object.id');
        $payment = OrderPayment::where('external_payment_id', $paymentId)->first();

        // Если API подтвердил успех — вызываем markAsPaid()
        if ($payment && $payment->status === 'succeeded') {
            $orderService->markAsPaid($payment->order);
        }

        return response()->json(['status' => 'success']);
    }

}
