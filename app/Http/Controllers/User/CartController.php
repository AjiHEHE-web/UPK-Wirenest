<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\User\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(CartService $cart): View
    {
        $items = $cart->rows();
        $total = $cart->fromMinorUnits($cart->totalCents($items));

        return view('pages.user.cart', [
            'page' => 'index',
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function store(Request $request, Product $product, CartService $cart): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart->add($product, (int) $validated['quantity']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'cartCount' => array_sum($cart->quantities()),
            ]);
        }

        return redirect()->route('user.cart.index')->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, Product $product, CartService $cart): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart->update($product, (int) $validated['quantity']);

        return redirect()->route('user.cart.index')->with('success', 'Jumlah produk diperbarui.');
    }

    public function destroy(Product $product, CartService $cart): RedirectResponse
    {
        $cart->remove($product);

        return redirect()->route('user.cart.index')->with('success', 'Produk dihapus dari keranjang.');
    }

    public function clear(CartService $cart): RedirectResponse
    {
        $cart->clear();

        return redirect()->route('user.cart.index')->with('success', 'Keranjang berhasil dikosongkan.');
    }
}
