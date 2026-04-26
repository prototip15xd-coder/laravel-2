<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @property integer $user_id
 * @property integer $total
 * @property string $status
 * @property string $shipping_address
 */
class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'user_id',
        'total',
        'status',
        'shipping_address',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function items() { return $this->hasMany(OrderItem::class); }
}
