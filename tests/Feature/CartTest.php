<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_add_product_to_cart(): void
    {
        // Arrange
        $product = Product::factory()->create();

        // Act
        $response = $this->post("/cart/items/{$product->id}", [
            'quantity' => 1,
        ]);

        // Assert
        $response->assertRedirect('/login');
    }

    public function test_user_can_add_product_to_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 1500]);

        $response = $this
            ->actingAs($user)
            ->post("/cart/items/{$product->id}", [
                'quantity' => 2,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }
}
