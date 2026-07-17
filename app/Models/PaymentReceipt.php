<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReceipt extends Model
{
    protected $casts = [
        'request_payload' => 'json',
        'response_payload' => 'json',
    ];

    protected $fillable = [
        'order_payment_id',
        'external_receipt_id',
        'type',
        'status',
        'send_to_customer',
        'request_payload',
        'response_payload',
        'error_message',
    ];
}
