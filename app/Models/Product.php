<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\Searchable;

/**
 * @property string $name
 * @property string $description
 * @property float $price
 * @property string $image
 * @property integer $category_id
 * @property-read Category $category
 */
class Product extends Model
{
    use HasFactory;
    use Notifiable;
    use Searchable;
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

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

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
        ];
    }
}
