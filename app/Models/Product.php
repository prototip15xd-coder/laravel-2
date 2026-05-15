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
 * @property integer $category_id
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
        'category_id',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'timestamps' => 'datetime',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
