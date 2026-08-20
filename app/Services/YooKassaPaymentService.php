<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\PaymentReceipt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use YooKassa\Client;
use YooKassa\Common\Exceptions\ApiException;

class YooKassaPaymentService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();

        $this->client->setAuth(
            config('services.yookassa.shop_id'),
            config('services.yookassa.secret_key')
        );
    }

    public function createPaymentForOrder(Order $order): OrderPayment
    {
        $idempotenceKey = (string) Str::uuid();

        $payload = [
            'amount' => [
                'value' => number_format((float) $order->total, 2, '.', ''),
                'currency' => config('services.yookassa.currency', 'RUB'),
            ],
            'capture' => true,
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => route('payments.yookassa.return', $order),
            ],
            'description' => 'Оплата заказа #' . $order->id,
            'metadata' => [
                'order_id' => $order->id,
            ],
        ];

        $response = $this->client->createPayment($payload, $idempotenceKey);

        return OrderPayment::create([
            'order_id' => $order->id,
            'provider' => 'yookassa',
            'status' => $response->getStatus(),
            'amount' => $order->total,
            'currency' => config('services.yookassa.currency', 'RUB'),
            'external_payment_id' => $response->getId(),
            'idempotence_key' => $idempotenceKey,
            'confirmation_url' => $response->getConfirmation()?->getConfirmationUrl(),
            'response_payload' => $response->jsonSerialize(),
        ]);
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

        Log::info('Webhook: статус платежа', [
            'payment_status' => $payment->status,
        ]);

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

        $response = $this->client->getPaymentInfo($payment->external_payment_id);


        // Обновляем локальный статус
        $payment->status = $response->getStatus();
        $payment->response_payload = $response->jsonSerialize();

        if ($response->getStatus() === 'succeeded') {
            $payment->paid_at = now();
            $payment->save();
            $payment->load('order');
            $order = $payment->order;

            if ($order && $order->status !== Order::STATUS_PAID) {
                $orderService = app(OrderService::class);
                $orderService->markAsPaid($order);
            }

            \Log::info('FetchPayment YooKasPaymServ 4 order', [
                'order' => $order,
                'order status' => $order->status,
            ]);

        } elseif ($response->getStatus() === 'canceled') {
            $payment->canceled_at = now();
            $payment->save();
        } else {
            $payment->save();
        }
    }

    public function synchronizePayment(OrderPayment $payment): void
    {
        //        Log::info('Синхронизация платежа', ['payment_id' => $payment->id]);

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

        // Формируем items как раньше
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'description' => $item->product->name ?? 'Товар',
                'quantity' => (string) $item->quantity,
                'amount' => [
                    'value' => number_format((float)$item->price, 2, '.', ''),
                    'currency' => 'RUB',
                ],
                'vat_code' => 2,
                'payment_mode' => 'full_payment',
                'payment_subject' => 'commodity',
            ];
        }

        $payload = [
            'payment_id' => $payment->external_payment_id,
            'type' => 'payment',
            'items' => $items,
            'tax_system_code' => 1,
            'customer' => [
                'email' => $order->user->email,
            ],
            'send' => true,
            'settlements' => [
                [
                    'type' => 'cashless',
                    'amount' => [
                        'value' => number_format((float)$order->total, 2, '.', ''),
                        'currency' => 'RUB',
                    ],
                ],
            ],
        ];

        $client = new Client();
        $client->setAuth(
            config('services.yookassa.shop_id'),
            config('services.yookassa.secret_key')
        );

        try {
            $receiptObject = $client->createReceipt($payload, Str::uuid()->toString());
        } catch (ApiException $e) {
            Log::error('Ошибка создания чека (SDK)', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            // Сохраняем ошибку в локальную запись
            $receipt = PaymentReceipt::create([
                'order_payment_id' => $payment->id,
                'type' => 'payment',
                'status' => 'canceled',
                'send_to_customer' => true,
                'request_payload' => $payload,
                'response_payload' => null,
                'error_message' => $e->getMessage(),
            ]);
            return;
        }

        // Сохраняем успешный чек
        $receipt = PaymentReceipt::create([
            'order_payment_id' => $payment->id,
            'type' => 'payment',
            'status' => $receiptObject->getStatus(),
            'send_to_customer' => true,
            'request_payload' => $payload,
            'response_payload' => $receiptObject->toArray(),
            'external_receipt_id' => $receiptObject->getId(),
            'error_message' => null,
        ]);

        Log::info('Чек создан (SDK)', ['receipt_id' => $receipt->id]);
    }

}
