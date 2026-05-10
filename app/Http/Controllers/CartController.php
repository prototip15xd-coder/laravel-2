<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\SessionCartService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private SessionCartService $sessionCartService,
    ) {
    }

    public function index(): Factory|View
    {
        return view('cart.index', [
            'items' => $this->sessionCartService->getItems(),
            'totalQuantity' => $this->sessionCartService->getTotalQuantity(),
            'totalPrice' => $this->sessionCartService->getTotalPrice(),
        ]);
    }

    public function store(Product $product, Request $request, SessionCartService $cart)
    {
        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $this->sessionCartService->add($product, (int) ($data['quantity'] ?? 1));

        return $this->respond($request, $cart);
    }

    public function update(Product $product, Request $request, SessionCartService $cart)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $this->sessionCartService->setQuantity($product, (int) $data['quantity']);

        return $this->respond($request, $cart);
    }

    public function destroy(Product $product, Request $request, SessionCartService $cart)
    {
        $this->sessionCartService->remove($product);
        return $this->respond($request, $cart);
    }

    public function clear(Request $request, SessionCartService $cart)
    {
        $this->sessionCartService->clear();
        return $this->respond($request, $cart);
    }

    private function respond(Request $request, SessionCartService $cart)
    {
        $payload = [
            'cartCount' => $cart->getTotalQuantity(),
        ];

        if ($request->expectsJson()) {
            $payload['html'] = view('cart._content', [
                'items' => $cart->getItems(),
                'totalQuantity' => $this->sessionCartService->getTotalQuantity(),
                'totalPrice' => $this->sessionCartService->getTotalPrice(),
            ])->render();

            return response()->json($payload);
        }

        return redirect()
            ->back()
            ->with('cartCount', $payload['cartCount']);
    }


}
