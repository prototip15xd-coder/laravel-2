<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Interface\CartServiceInterface;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class DatabseCartService implements CartServiceInterface
{
    public function getItems(): array|null
    {
        $cart = Cart::where('user_id', auth()->id())->where('status', 'active')->first();

        if ($cart) {
            $cartItem = CartItem::where('cart_id', $cart->id)->with('product')->get();
            return $cartItem;
        }

        return null;
    }

    public function getItem(Product $product): CartItem|null
    {
        $cart = Cart::where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();
        if ($cart) {
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->with('quantity')
                ->first();
            return $cartItem;
        }

        return null;
    }

    public function getTotalQuantity(): int
    {
        $cartItems = $this->getItems();
        if (!$cartItems || $cartItems->isEmpty()) {
            return 0;
        }
        return $cartItems->sum('quantity');
    }

    public function getTotalPrice(): string
    {
        $total = '0.00';
        $items = $this->getItems();
        if (!$items || $items->isEmpty()) {
            return '0.00';
        }
        foreach ($items as $item) {
            $total = bcadd($total, $item['subtotal'], 2);
        }

        return $total;
    }

    public function add(Product $product, int $quantity = 1): int
    {
        $quantity = max(1, $quantity);

        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id(),
            'status' => 'active',
        ]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $next = $cartItem->quantity + $quantity;
            $next = min($next, $product->stock);
            $cartItem->update(['quantity' => $next]);
        } else {
            $next = min($quantity, $product->stock);
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $next,
                'price' => $product->price,
            ]);
        }

        return $cartItem->quantity;
    }

    public function setQuantity(Product $product, int $quantity): int
    {
        $quantity = max(0, $quantity);

        if ($product->stock <= 0 || $quantity === 0) {
            $this->removeById($product->id);
            return 0;
        }

        $quantity = min($quantity, $product->stock);

        $cartItem = $this->getItem($product);
        if ($cartItem) {
            $cartItem->update([
                'quantity' => $quantity,
                'price' => $product->price
            ]);
        } else {
            $cart = Cart::where('user_id', auth()->id())
                ->where('status', 'active')
                ->first();

            if ($cart) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
            }
        }
        return $quantity;
    }

    public function remove(Product $product): void
    {
        $this->removeById($product->id);
    }

    private function removeById(int $productId): void
    {
        $cart = Cart::where('user_id', auth()->id())->where('status', 'active')->first();
        if ($cart) {
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->delete();
        }
    }

    public function clear(): void
    {
        $cart = Cart::where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();
        if ($cart) {
            $cart->items()->delete();
            $cart->delete();
        }
    }
}
