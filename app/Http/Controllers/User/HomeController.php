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
                ->get(),
            'featuredProducts' => Product::query()
                ->with('category')
                ->where('stock_produk', '>', 0)
                ->latest('created_at')
                ->limit(8)
                ->get(),
        ]);
    }
}
