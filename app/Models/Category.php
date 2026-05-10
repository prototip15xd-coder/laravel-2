<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $slug
 */
class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'id',
        'name',
        'slug',
    ];

    public function products()
    {
        return $this->hasMany(Product::class); /// категория должна принадлежать товару
    }


}
