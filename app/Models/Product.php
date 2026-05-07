<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $name
 * @property string $description
 * @property float $price
 * @property string $image
 */
class Product extends Model
{
    use HasFactory;
    use Notifiable;

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
