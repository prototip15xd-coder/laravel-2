<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Product;

class AdminProductService
{
    public function create(array $data): Product
    {
        return Product::create([
            'name' => $data['name'],
            'description'  => $data['description'],
            'price'      => $data['price'],
            'image'     => $data['image'],
            'category_id'    => $data['category_id'],
            'stock'   => $data['stock'],
        ]);
    }

    // app/Services/Admin/AdminUserService.php
    public function updateProduct(Product $product, array $data): Product
    {
        $product->update([
            'name' => $data['name'],
            'description'  => $data['description'],
            'price'      => $data['price'],
            'image'     => $data['image'],
            'category_id'    => $data['category_id'],
            'stock'   => $data['stock'],
        ]);

        return $product;
    }

}
