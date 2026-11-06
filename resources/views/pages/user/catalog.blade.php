@extends('layouts.user')

@section('title', $page === 'index' ? 'Katalog bunga' : $product->name_produk)

@section('content')
    @if ($page === 'index')
        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-botanical-700">Koleksi kami</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-ink">Temukan rangkaian yang tepat</h1>
                    <p class="mt-2 text-sm text-stone-600">Bunga pilihan untuk menyampaikan perasaan di setiap momen.</p>
                </div>
                <a href="{{ route('user.cart.index') }}" class="inline-flex min-h-11 items-center gap-2 rounded-full border border-line bg-white px-4 text-sm font-semibold text-ink hover:border-botanical-300">
                    Keranjang <span aria-hidden="true">→</span>
                </a>
            </div>

            <form method="GET" action="{{ route('user.catalog.index') }}" class="mt-7 grid gap-3 rounded-2xl border border-line bg-cream/50 p-4 sm:grid-cols-[1fr_240px_auto]">
                <div>
                    <label for="catalog-search" class="sr-only">Cari bunga atau kategori</label>
                    <input id="catalog-search" name="search" value="{{ $filters['search'] ?? '' }}" type="search" placeholder="Cari nama bunga atau kategori..." class="h-12 w-full rounded-xl border-line bg-white text-sm focus:border-botanical-500 focus:ring-botanical-500">
                </div>
                <div>
                    <label for="catalog-category" class="sr-only">Kategori</label>
                    <select id="catalog-category" name="category" class="h-12 w-full rounded-xl border-line bg-white text-sm focus:border-botanical-500 focus:ring-botanical-500">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id_kategori }}" @selected(($filters['category'] ?? '') === $category->id_kategori)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="min-h-12 rounded-xl bg-botanical-800 px-6 text-sm font-semibold text-white transition hover:bg-botanical-900">Cari produk</button>
            </form>

            <div class="mt-8 flex items-center justify-between gap-4">
                <p class="text-sm text-stone-600">{{ $products->total() }} produk ditemukan</p>
                @if (($filters['search'] ?? false) || ($filters['category'] ?? false))
                    <a href="{{ route('user.catalog.index') }}" class="text-sm font-semibold text-botanical-800 hover:underline">Hapus filter</a>
                @endif
            </div>
            <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse ($products as $product)
                    <article class="group overflow-hidden rounded-2xl border border-line bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <a href="{{ route('user.catalog.show', $product) }}" class="block">
                            <div class="aspect-[4/3] overflow-hidden bg-botanical-50">
                                @if ($product->image)
                                    <img src="{{ Storage::disk('public')->url($product->image) }}" alt="{{ $product->name_produk }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy">
                                @else
                                    <div class="flex h-full items-center justify-center text-botanical-300" aria-hidden="true">
                                        <svg class="h-14 w-14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Zm0 0c2-2 5-2 7 0-2 2-5 2-7 0Zm0 0c2 2 2 5 0 7-2-2-2-5 0-7Zm0 0c-2 2-5-2-7 0 2-2 5-2 7 0Z"/><circle cx="12" cy="12" r="1.4"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <p class="text-xs font-medium text-stone-500">{{ $product->category?->name ?? 'Rangkaian bunga' }}</p>
                                <h2 class="mt-1 truncate font-semibold text-ink">{{ $product->name_produk }}</h2>
                                <p class="mt-2 font-semibold text-botanical-900">Rp {{ number_format((float) $product->harga_produk, 2, ',', '.') }}</p>
                            </div>
                        </a>
                        <div class="flex items-center justify-between border-t border-line px-4 py-3">
                            <span class="text-xs text-stone-500">{{ $product->stock_produk }} tersedia</span>
                            <div class="flex items-center gap-2">
                                @if (in_array($product->id_produk, $favoriteIds, true))
                                    <form method="POST" action="{{ route('user.favorites.destroy', $product) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-rose-600 hover:bg-rose-50" aria-label="Hapus {{ $product->name_produk }} dari favorit">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m12 21-1.45-1.32C5.4 15.05 2 11.97 2 8.2 2 5.12 4.42 2.7 7.5 2.7c1.74 0 3.41.81 4.5 2.09a6.05 6.05 0 0 1 4.5-2.09c3.08 0 5.5 2.42 5.5 5.5 0 3.77-3.4 6.85-8.55 11.49L12 21Z"/></svg>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('user.favorites.store', $product) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-stone-500 hover:bg-rose-50 hover:text-rose-600" aria-label="Tambahkan {{ $product->name_produk }} ke favorit">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m12 21-1.45-1.32C5.4 15.05 2 11.97 2 8.2 2 5.12 4.42 2.7 7.5 2.7c1.74 0 3.41.81 4.5 2.09a6.05 6.05 0 0 1 4.5-2.09c3.08 0 5.5 2.42 5.5 5.5 0 3.77-3.4 6.85-8.55 11.49L12 21Z"/></svg>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('user.cart.store', $product) }}">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="inline-flex min-h-10 items-center gap-2 rounded-full bg-botanical-800 px-4 text-xs font-semibold text-white transition hover:bg-botanical-900"><span aria-hidden="true">+</span> Keranjang</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-botanical-200 bg-cream/50 px-6 py-16 text-center">
                        <h2 class="font-semibold text-ink">Produk belum ditemukan</h2>
                        <p class="mt-2 text-sm text-stone-600">Coba kata kunci atau kategori yang berbeda.</p>
                    </div>
                @endforelse
            </div>
            @if ($products->hasPages())
                <div class="mt-8">{{ $products->links() }}</div>
            @endif
        </section>
    @else
        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <a href="{{ route('user.catalog.index') }}" class="text-sm font-semibold text-botanical-800 hover:underline">← Kembali ke katalog</a>
            <div class="mt-5 grid gap-8 lg:grid-cols-2 lg:gap-12">
                <div class="aspect-square overflow-hidden rounded-3xl bg-botanical-50">
                    @if ($product->image)
                        <img src="{{ Storage::disk('public')->url($product->image) }}" alt="{{ $product->name_produk }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center text-botanical-300" aria-hidden="true">
                            <svg class="h-28 w-28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.1"><path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Zm0 0c2-2 5-2 7 0-2 2-5 2-7 0Zm0 0c2 2 2 5 0 7-2-2-2-5 0-7Zm0 0c-2 2-5-2-7 0 2-2 5-2 7 0Z"/><circle cx="12" cy="12" r="1.4"/></svg>
                        </div>
                    @endif
                </div>
                <div class="flex flex-col justify-center">
                    <p class="text-sm font-semibold text-botanical-700">{{ $product->category?->name ?? 'Rangkaian bunga' }}</p>
                    <h1 class="mt-3 text-3xl font-semibold tracking-tight text-ink sm:text-4xl">{{ $product->name_produk }}</h1>
                    <p class="mt-4 text-2xl font-semibold text-botanical-800">Rp {{ number_format((float) $product->harga_produk, 2, ',', '.') }}</p>
                    <p class="mt-4 text-sm leading-7 text-stone-600">{{ $product->deskripsi_produk ?: 'Rangkaian bunga pilihan, dirangkai dengan penuh perhatian.' }}</p>
                    <p class="mt-5 text-sm text-stone-600">Stok tersedia: <span class="font-semibold text-ink">{{ $product->stock_produk }} unit</span></p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        @if ($product->stock_produk > 0)
                            <form method="POST" action="{{ route('user.cart.store', $product) }}" class="flex flex-wrap gap-2">
                                @csrf
                                <label for="quantity" class="sr-only">Jumlah</label>
                                <input id="quantity" type="number" name="quantity" min="1" max="{{ min(99, $product->stock_produk) }}" value="1" class="h-12 w-24 rounded-xl border-line text-center text-sm focus:border-botanical-500 focus:ring-botanical-500">
                                <button type="submit" class="min-h-12 rounded-xl bg-botanical-800 px-6 text-sm font-semibold text-white transition hover:bg-botanical-900">Tambah ke keranjang</button>
                            </form>
                        @else
                            <span class="rounded-xl bg-stone-100 px-5 py-3 text-sm font-semibold text-stone-500">Stok habis</span>
                        @endif
                        @if ($isFavorite)
                            <form method="POST" action="{{ route('user.favorites.destroy', $product) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="min-h-12 rounded-xl border border-rose-200 px-5 text-sm font-semibold text-rose-700 hover:bg-rose-50">Hapus favorit</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('user.favorites.store', $product) }}">
                                @csrf
                                <button type="submit" class="min-h-12 rounded-xl border border-line px-5 text-sm font-semibold text-ink hover:bg-cream">Simpan favorit</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            @if ($relatedProducts->isNotEmpty())
                <div class="mt-16">
                    <h2 class="text-2xl font-semibold text-ink">Pilihan serupa</h2>
                    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($relatedProducts as $relatedProduct)
                            <article class="group overflow-hidden rounded-2xl border border-line bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                                <a href="{{ route('user.catalog.show', $relatedProduct) }}" class="block">
                                    <div class="aspect-[4/3] overflow-hidden bg-botanical-50">
                                        @if ($relatedProduct->image)
                                            <img src="{{ Storage::disk('public')->url($relatedProduct->image) }}" alt="{{ $relatedProduct->name_produk }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy">
                                        @else
                                            <div class="flex h-full items-center justify-center text-botanical-300" aria-hidden="true">
                                                <svg class="h-14 w-14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Zm0 0c2-2 5-2 7 0-2 2-5 2-7 0Zm0 0c2 2 2 5 0 7-2-2-2-5 0-7Zm0 0c-2 2-5 2-7 0 2-2 5-2 7 0Z"/><circle cx="12" cy="12" r="1.4"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <p class="text-xs font-medium text-stone-500">{{ $relatedProduct->category?->name ?? 'Rangkaian bunga' }}</p>
                                        <h3 class="mt-1 truncate font-semibold text-ink">{{ $relatedProduct->name_produk }}</h3>
                                        <p class="mt-2 font-semibold text-botanical-900">Rp {{ number_format((float) $relatedProduct->harga_produk, 2, ',', '.') }}</p>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    @endif
@endsection
