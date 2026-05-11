<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\SessionCartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly SessionCartService $sessionCartService,
    ) {
    }

    public function index(SessionCartService $cart): Factory|View
    {
        $defaultAddress = null;
        if (Auth::check()) {
            $defaultAddress = Auth::user()
                ?->addresses()
                ->where('is_default', true)
                ->first();
        }

        return view('cart.index', [
            'items' => $cart->getItems(),
            'totalQuantity' => $cart->getTotalQuantity(),
            'totalPrice' => $cart->getTotalPrice(),
            'defaultAddress' => $defaultAddress,
        ]);
    }

    public function store(Product $product, Request $request)
    {
        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $this->sessionCartService->add($product, (int) ($data['quantity'] ?? 1));

        return $this->respond($request);
    }

    public function update(Product $product, Request $request)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $this->sessionCartService->setQuantity($product, (int) $data['quantity']);

        return $this->respond($request);
    }

    public function destroy(Product $product, Request $request)
    {
        $this->sessionCartService->remove($product);
        return $this->respond($request);
    }

    public function clear(Request $request)
    {
        $this->sessionCartService->clear();
        return $this->respond($request);
    }

    private function respond(Request $request)
    {
        $cart = $this->sessionCartService;
        $payload = [
            'cartCount' => $cart->getTotalQuantity(),
        ];

        if ($request->expectsJson()) {
            $payload['html'] = view('cart._content', [
                'items' => $cart->getItems(),
                'totalQuantity' => $cart->getTotalQuantity(),
                'totalPrice' => $cart->getTotalPrice(),
            ])->render();

            return response()->json($payload);
        }

        return redirect()
            ->back()
            ->with('cartCount', $payload['cartCount']);
    }


}
