<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.user.home', [
            'categories' => Category::query()
                ->whereHas('products', fn ($query) => $query->where('stock_produk', '>', 0))
                ->withCount(['products as available_products_count' => fn ($query) => $query->where('stock_produk', '>', 0)])
                ->orderBy('name')
                ->limit(8)
                ->get(),
            'featuredProducts' => Product::query()
                ->select('id_produk', 'id_kategori', 'name_produk', 'harga_produk', 'stock_produk', 'image', 'created_at')
                ->with('category:id_kategori,name')
                ->where('stock_produk', '>', 0)
                ->latest('created_at')
                ->limit(4)
                ->get(),
        ]);
    }
}
