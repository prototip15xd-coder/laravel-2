<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $order_id
 * @property integer $product_id
 * @property integer $quantity
 * @property float $price
 */
class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function order() { return $this->belongsTo(Order::class); }

    public function product() { return $this->belongsTo(Product::class); }
}
