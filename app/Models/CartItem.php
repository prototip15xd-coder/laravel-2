<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
