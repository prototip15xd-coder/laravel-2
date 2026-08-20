<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailySalesReport extends Model
{
    protected $fillable = [
        'report_date',
        'orders_count',
        'sales_count',
        'revenue',
        'canceled_count',
        'calculated_at',
        'average_order_value',
        'pending_orders_count',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'revenue' => 'decimal:2',
            'calculated_at' => 'datetime',
        ];
    }
}
