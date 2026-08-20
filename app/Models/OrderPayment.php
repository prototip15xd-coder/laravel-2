<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read Order $order
 */
class OrderPayment extends Model
{
    protected $fillable = [
        'order_id',
        'provider',
        'status',
        'amount',
        'currency',
        'external_payment_id',
        'idempotence_key',
        'confirmation_url',
        'request_payload',
        'response_payload',
        'paid_at',
        'canceled_at',
        'error_message',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
