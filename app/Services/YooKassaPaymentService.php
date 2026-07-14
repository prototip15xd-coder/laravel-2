<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\PaymentReceipt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YooKassaPaymentService
{
    public function __construct(
    ) {
    }

    public function createPaymentForOrder(Order $order): OrderPayment
    {
        if ($order->payment_method === 'cash') {
            throw new \Exception('Оплата наличными не требует создания платежа в YooKassa.');
        }

        $payment = OrderPayment::create([
            'order_id' => $order->id,
            'provider' => 'YooKassa',
            'status' => $order->status,
            'amount' => $order->total,
            'currency' => 'RUB', ///точно? как работать с валютой оплаты
        ]);

        $payload = [
            'amount' => [
                'value' => number_format($order->total, 2, '.', ''),
                'currency' => 'RUB',
            ],
            'capture' => true,
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => route('orders.show', $order),
            ],
            'description' => "Заказ №{$order->id}",
            'metadata' => [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
            ],
        ];

        // Отправляем запрос в YooKassa
        $response = Http::withBasicAuth(config('services.yookassa.shop_id'), config('services.yookassa.secret_key'))
            ->post('https://api.yookassa.ru/v3/payments', $payload);

        // Сохраняем запрос и ответ
        $payment->request_payload = $payload;
        $payment->response_payload = $response->json();

        if ($response->failed()) {
            $payment->status = 'canceled';
            $payment->error_message = $response->body();
            $payment->save();
            throw new \Exception('Ошибка создания платежа: ' . $response->body());
        }

        $data = $response->json();

        // Обновляем локальную запись
        $payment->external_payment_id = $data['id'];
        $payment->status = $data['status'];
        $payment->confirmation_url = $data['confirmation']['confirmation_url'];
        $payment->save();

        return $payment;
    }

    public function handleWebhook(array $webhookData): void
    {
        $event = $webhookData['event'] ?? null;
        $paymentData = $webhookData['object'] ?? null;

        if (!$event || !$paymentData) {
            Log::warning('Webhook: некорректные данные', $webhookData);
            return;
        }

        $externalPaymentId = $paymentData['id'];

        // Находим локальный платёж
        $payment = OrderPayment::where('external_payment_id', $externalPaymentId)->first();

        if (!$payment) {
            Log::warning('Webhook: платёж не найден', ['external_id' => $externalPaymentId]);
            return;
        }

        // Проверяем статус через API YooKassa (НЕ верим вебхуку)
        $this->fetchPayment($payment);
        $payment->refresh();

        // бновляем локальный статус на основе ответа API
        if ($payment->status === 'succeeded') {
            $payment->paid_at = now();
            $payment->save();

            // Создаём чек
            $this->createReceipt($payment);

            Log::info('Webhook: платёж успешен (подтверждён API)', [
                'payment_id' => $payment->id,
                'external_id' => $payment->external_payment_id,
            ]);

        } elseif ($payment->status === 'canceled') {
            $payment->canceled_at = now();
            $payment->save();

            Log::info('Webhook: платёж отменён (подтверждён API)', [
                'payment_id' => $payment->id,
                'external_id' => $payment->external_payment_id,
            ]);

        } else {
            $payment->save();

            Log::info('Webhook: обновлён статус платежа (подтверждён API)', [
                'payment_id' => $payment->id,
                'status' => $payment->status,
            ]);
        }
    }

    public function fetchPayment(OrderPayment $payment): void
    {
        if (!$payment->external_payment_id) {
            throw new \Exception('У платежа нет external_payment_id');
        }

        $response = Http::withBasicAuth(config('services.yookassa.shop_id'), config('services.yookassa.secret_key'))
            ->get("https://api.yookassa.ru/v3/payments/{$payment->external_payment_id}");

        if ($response->failed()) {
            Log::error('Ошибка получения статуса платежа', [
                'payment_id' => $payment->id,
                'error' => $response->body(),
            ]);
            return;
        }

        $data = $response->json();

        // Обновляем локальный статус
        $payment->status = $data['status'];
        $payment->response_payload = $data;

        if ($data['status'] === 'succeeded') {
            $payment->paid_at = now();
            $payment->save();

            $order = $payment->order;
            if ($order->status !== Order::STATUS_PAID) {
                $this->orderService->markAsPaid($order);
            }
        } elseif ($data['status'] === 'canceled') {
            $payment->canceled_at = now();
            $payment->save();
        } else {
            $payment->save();
        }
    }

    public function synchronizePayment(OrderPayment $payment): void
    {
        Log::info('Синхронизация платежа', ['payment_id' => $payment->id]);

        if (!$payment->external_payment_id) {
            Log::warning('Платеж не имеет external_payment_id, пропускаем', ['payment_id' => $payment->id]);
            return;
        }

        $oldStatus = $payment->status;

        // Запрашиваем актуальный статус
        $this->fetchPayment($payment);

        $payment->refresh();
        $newStatus = $payment->status;

        Log::info('Синхронизация завершена', [
            'payment_id' => $payment->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);
    }

    public function createReceipt(OrderPayment $payment): void
    {
        $order = $payment->order;

        // Собираем товары для чека
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'description' => $item->product->name ?? 'Товар',
                'quantity' => (string) $item->quantity,
                'amount' => [
                    'value' => number_format($item->price, 2, '.', ''),
                    'currency' => 'RUB',
                ],
                'vat_code' => 2, // Без НДС (20% для России)
                'payment_mode' => 'full_payment',
                'payment_subject' => 'commodity',
            ];
        }

        $payload = [
            'payment_id' => $payment->external_payment_id,
            'items' => $items,
            'tax_system_code' => 1,
            'customer' => [
                'email' => $order->user->email,
            ],
            'send' => true,
        ];

        // Отправляем запрос на создание чека
        $response = Http::withBasicAuth(config('services.yookassa.shop_id'), config('services.yookassa.secret_key'))
            ->post('https://api.yookassa.ru/v3/receipts', $payload);

        // Сохраняем в базу
        $receipt = PaymentReceipt::create([
            'order_payment_id' => $payment->id,
            'type' => 'payment',
            'status' => $response->successful() ? 'succeeded' : 'pending',
            'send_to_customer' => true,
            'request_payload' => $payload,
            'response_payload' => $response->json(),
            'error_message' => $response->failed() ? $response->body() : null,
        ]);

        if ($response->failed()) {
            $receipt->status = 'canceled';
            $receipt->error_message = $response->body();
            $receipt->save();

            Log::error('Ошибка создания чека', [
                'payment_id' => $payment->id,
                'error' => $response->body(),
            ]);
            return;
        }

        $data = $response->json();
        $receipt->external_receipt_id = $data['id'];
        $receipt->status = $data['status'];
        $receipt->save();

        Log::info('Чек создан', ['receipt_id' => $receipt->id, 'external_id' => $receipt->external_receipt_id]);
    }

}
