<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $products = Product::query()
            ->with('category')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('name_produk', 'like', '%'.$search.'%')
                    ->orWhereHas('category', fn ($category) => $category->where('name', 'like', '%'.$search.'%'));
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id_produk')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.products', [
            'page' => 'index',
            'products' => $products,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.products', [
            'page' => 'create',
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $attributes = $request->validated();
        $image = $attributes['image'];
        unset($attributes['image']);

        $path = $image->store('products', 'public');

        if ($path === false) {
            throw new RuntimeException('Gambar produk gagal disimpan.');
        }

        $product = Product::create([
            ...$attributes,
            'id_produk' => (string) Str::uuid(),
            'image' => $path,
        ]);

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product): View
    {
        $product->load('category');

        return view('pages.admin.products', [
            'page' => 'show',
            'product' => $product,
        ]);
    }

    public function edit(Product $product): View
    {
        return view('pages.admin.products', [
            'page' => 'edit',
            'product' => $product,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $attributes = $request->validated();
        $image = $attributes['image'] ?? null;
        unset($attributes['image']);
        $oldImage = $product->image;

        if ($image !== null) {
            $path = $image->store('products', 'public');

            if ($path === false) {
                throw new RuntimeException('Gambar produk gagal disimpan.');
            }

            $attributes['image'] = $path;
        }

        try {
            DB::transaction(fn () => $product->update($attributes));
        } catch (\Throwable $exception) {
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        if (isset($path) && $oldImage !== null) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Informasi produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orderItems()->exists() || DB::table('favorites')->where('id_produk', $product->id_produk)->exists()) {
            return back()->with('error', 'Produk yang sudah digunakan pada pesanan atau favorit tidak dapat dihapus.');
        }

        $image = $product->image;
        $product->delete();

        if ($image !== null) {
            Storage::disk('public')->delete($image);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
