<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $name
 * @property string $description
 * @property float $price
 * @property string $image
 */
class Product extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'timestamps' => 'datetime',
        ];
    }
}
