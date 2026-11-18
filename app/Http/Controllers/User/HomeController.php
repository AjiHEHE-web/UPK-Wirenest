<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $featuredProducts = Product::query()
            ->select('id_produk', 'id_kategori', 'name_produk', 'deskripsi_produk', 'harga_produk', 'stock_produk', 'image', 'created_at')
            ->with('category:id_kategori,name')
            ->where('stock_produk', '>', 0)
            ->latest('created_at')
            ->limit(4)
            ->get();

        $favoriteIds = $request->user()
            ? $request->user()->favoriteRecords()
                ->whereIn('id_produk', $featuredProducts->pluck('id_produk'))
                ->pluck('id_produk')
                ->all()
            : [];

        return view('pages.user.home', [
            'categories' => Category::query()
                ->whereHas('products', fn ($query) => $query->where('stock_produk', '>', 0))
                ->withCount(['products as available_products_count' => fn ($query) => $query->where('stock_produk', '>', 0)])
                ->orderBy('name')
                ->limit(8)
                ->get(),
            'featuredProducts' => $featuredProducts,
            'favoriteIds' => $favoriteIds,
        ]);
    }
}
