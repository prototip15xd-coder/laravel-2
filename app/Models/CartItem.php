<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @property integer $cart_id
 * @property integer $product_id
 * @property integer $quantity
 * @property float $price
 */
class CartItem extends Model
{
    protected $table = 'cart_items';
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function cart() { return $this->belongsTo(Cart::class); }

    public function product() { return $this->belongsTo(Product::class); }
}
