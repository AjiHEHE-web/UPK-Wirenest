<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', Rule::exists('kategori', 'id_kategori')],
        ]);

        $products = Product::query()
            ->with('category')
            ->where('stock_produk', '>', 0)
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name_produk', 'like', '%'.$search.'%')
                        ->orWhere('deskripsi_produk', 'like', '%'.$search.'%')
                        ->orWhereHas('category', fn ($category) => $category->where('name', 'like', '%'.$search.'%'));
                });
            })
            ->when($filters['category'] ?? null, fn ($query, string $categoryId) => $query->where('id_kategori', $categoryId))
            ->orderByDesc('created_at')
            ->orderBy('id_produk')
            ->paginate(12)
            ->withQueryString();

        return view('pages.user.catalog', [
            'page' => 'index',
            'products' => $products,
            'categories' => Category::query()->orderBy('name')->get(),
            'filters' => $filters,
            'favoriteIds' => $request->user()->favoriteRecords()->pluck('id_produk')->all(),
        ]);
    }

    public function show(Request $request, Product $product): View
    {
        $product->load('category');

        return view('pages.user.catalog', [
            'page' => 'show',
            'product' => $product,
            'isFavorite' => $request->user()->favoriteRecords()->where('id_produk', $product->id_produk)->exists(),
            'favoriteIds' => $request->user()->favoriteRecords()->pluck('id_produk')->all(),
            'relatedProducts' => Product::query()
                ->with('category')
                ->where('id_kategori', $product->id_kategori)
                ->where('stock_produk', '>', 0)
                ->whereKeyNot($product->getKey())
                ->limit(4)
                ->get(),
        ]);
    }
}
