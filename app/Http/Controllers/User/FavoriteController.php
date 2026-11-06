<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $favorites = $request->user()
            ->favoriteRecords()
            ->with('product.category')
            ->latest('created_at')
            ->paginate(12);

        return view('pages.user.favorites', [
            'page' => 'index',
            'favorites' => $favorites,
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->user()->favoriteRecords()->firstOrCreate(
            ['id_produk' => $product->id_produk],
            [
                'id_favorit' => (string) Str::uuid(),
                'created_at' => now(),
            ],
        );

        return redirect()->route('user.favorites.index')->with('success', 'Produk ditambahkan ke favorit.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $request->user()->favoriteRecords()->where('id_produk', $product->id_produk)->delete();

        return redirect()->route('user.favorites.index')->with('success', 'Produk dihapus dari favorit.');
    }
}
