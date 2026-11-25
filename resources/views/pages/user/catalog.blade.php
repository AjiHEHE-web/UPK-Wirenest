@extends('layouts.user')

@section('title', $page === 'index' ? 'Katalog buket' : $product->name_produk)

@section('content')
    @if ($page === 'index')
        <section class="bg-transparent px-4 pb-14 pt-10 sm:px-6 sm:pb-16 sm:pt-12 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="inline-flex items-center gap-2 rounded-full bg-matcha px-4 py-1.5 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#a8942f] shadow-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Zm0 0c2-2 5-2 7 0-2 2-5 2-7 0Zm0 0c2 2 2 5 0 7-2-2-2-5 0-7Zm0 0c-2-2-5-2-7 0 2 2 5 2 7 0Z"/><circle cx="12" cy="12" r="1.4"/></svg>
                        Koleksi artisan WireNest
                    </p>
                    <h1 class="mt-3 font-display text-4xl font-semibold leading-tight text-[#34873a] sm:text-5xl">Buket handmade kami</h1>
                    <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-stone-600 sm:text-base">Setiap kelopak dirangkai dengan teliti dari batang chenille yang lembut dan tahan lama.</p>
                </div>

                <form method="GET" action="{{ route('user.catalog.index') }}" data-ajax-search data-ajax-results="catalog-results" class="mx-auto mt-6 max-w-5xl">
                    <div class="flex min-h-[54px] min-w-0 items-center gap-3 rounded-full border border-white/70 bg-white px-5 shadow-[0_8px_24px_rgba(49,79,56,.09)]">
                        <svg class="h-5 w-5 shrink-0 text-stone-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/></svg>
                        <label for="catalog-search" class="sr-only">Cari buket</label>
                        <input id="catalog-search" name="search" value="{{ $filters['search'] ?? '' }}" type="search" placeholder="Cari bunga ....." class="h-12 w-full min-w-0 border-0 bg-transparent text-sm placeholder:text-stone-400 focus:ring-0">
                    </div>
                    <input type="hidden" name="category" value="{{ $filters['category'] ?? '' }}" data-catalog-category>
                    <div class="mt-3 flex flex-wrap justify-center gap-2" aria-label="Pilih kategori buket">
                        <button type="button" data-catalog-category-option="" aria-pressed="{{ empty($filters['category']) ? 'true' : 'false' }}" @class(['inline-flex min-h-11 w-28 items-center justify-center whitespace-nowrap rounded-full px-3 text-sm font-medium transition', 'bg-[#356b50] text-white' => empty($filters['category']), 'bg-white text-[#356b50] hover:bg-white/80' => ! empty($filters['category'])])>Semua</button>
                        @foreach ($categories as $category)
                            <button type="button" data-catalog-category-option="{{ $category->id_kategori }}" aria-pressed="{{ ($filters['category'] ?? '') === $category->id_kategori ? 'true' : 'false' }}" @class(['inline-flex min-h-11 w-28 items-center justify-center whitespace-nowrap rounded-full px-3 text-sm font-medium transition', 'bg-[#356b50] text-white' => ($filters['category'] ?? '') === $category->id_kategori, 'bg-white text-[#356b50] hover:bg-white/80' => ($filters['category'] ?? '') !== $category->id_kategori])>{{ $category->name }}</button>
                        @endforeach
                    </div>
                </form>
                <div id="catalog-results" data-ajax-results aria-live="polite">
                <div class="mt-7 text-sm">
                    <p data-ajax-result-count class="text-stone-600"><span class="font-semibold text-botanical-800">{{ $products->total() }}</span> buket ditemukan</p>
                </div>

                <div class="motion-stagger mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @forelse ($products as $product)
                        <x-user.product-card :product="$product" :is-favorite="in_array($product->id_produk, $favoriteIds, true)" />
                    @empty
                        <div class="col-span-full rounded-[26px] border border-dashed border-botanical-300 bg-matcha/80 px-6 py-16 text-center">
                            <h2 class="font-display text-2xl font-semibold text-[#34873a]">Buket belum ditemukan</h2>
                            <p class="mt-2 text-sm text-stone-600">Coba kata kunci atau kategori yang berbeda.</p>
                        </div>
                    @endforelse
                </div>
                @if ($products->hasPages())
                    <div class="mt-8"><x-user.pagination :paginator="$products" /></div>
                @endif
                </div>
            </div>
        </section>
    @else
        <section class="bg-transparent px-4 pb-14 pt-8 sm:px-6 sm:pb-16 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <a href="{{ route('user.catalog.index') }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-botanical-800 transition hover:text-botanical-950"><span aria-hidden="true">←</span> Kembali ke buket</a>
                <div class="mt-3 grid items-start gap-6 lg:grid-cols-[1.15fr_0.85fr] lg:gap-8">
                    <div class="overflow-hidden rounded-[30px] border-[10px] border-white bg-botanical-100 shadow-[0_18px_48px_rgba(42,67,48,.12)]">
                        <div class="aspect-[0.95/1]">
                            @if ($product->image)
                                <img src="{{ Storage::disk('public')->url($product->image) }}" alt="{{ $product->name_produk }}" class="h-full w-full object-cover" loading="eager" fetchpriority="high" decoding="async">
                            @else
                                <div class="flex h-full items-center justify-center"><x-application-logo class="h-40 w-60" /></div>
                            @endif
                        </div>
                    </div>

                    <section class="rounded-[30px] bg-matcha p-6 shadow-[0_12px_34px_rgba(47,75,55,.08)] sm:p-8">
                        <p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-[#a8942f]"><span aria-hidden="true">✦</span>{{ $product->category?->name ?? 'Koleksi pilihan' }}</p>
                        <h1 class="mt-3 font-display text-4xl font-semibold leading-tight text-[#34873a] sm:text-[44px]">{{ $product->name_produk }}</h1>
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                            <p class="font-display text-2xl font-semibold text-[#34873a]">Rp {{ number_format((float) $product->harga_produk, 0, ',', '.') }}</p>
                            <span @class(['rounded-full px-3 py-1.5 text-xs font-semibold', 'bg-[#f8e5eb] text-rose-800' => $product->stock_produk <= 5, 'bg-[#e2f4dd] text-botanical-800' => $product->stock_produk > 5])><span class="mr-1 inline-block h-2 w-2 rounded-full bg-current"></span> Stok {{ $product->stock_produk }} tersedia</span>
                        </div>
                        <p class="mt-5 text-sm leading-7 text-stone-600">{{ $product->deskripsi_produk ?: 'Buket chenille handmade yang dirangkai dengan penuh perhatian. Hadiah lembut dan tahan lama untuk momen yang berarti.' }}</p>

                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            @guest
                                <a href="{{ route('login') }}" class="inline-flex min-h-12 flex-1 items-center justify-center rounded-full bg-[#34873a] px-5 text-sm font-semibold text-white transition hover:bg-botanical-800">Masuk untuk memesan</a>
                            @else
                            @if ($product->stock_produk > 0)
                                <form method="POST" action="{{ route('user.cart.store', $product) }}" data-ajax-cart class="flex min-w-0 flex-1 flex-wrap gap-2">
                                    @csrf
                                    <label for="quantity" class="sr-only">Jumlah</label>
                                    <input id="quantity" type="number" name="quantity" min="1" max="{{ min(99, $product->stock_produk) }}" value="1" class="h-12 w-20 rounded-full border-line bg-[#fff9fb] text-center text-sm focus:border-botanical-500 focus:ring-botanical-500">
                                    <button type="submit" class="inline-flex min-h-12 flex-1 items-center justify-center gap-2 rounded-full bg-[#34873a] px-4 text-sm font-semibold text-white shadow-[0_6px_16px_rgba(65,95,72,.18)] transition hover:bg-botanical-800"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 4h2l2 12h11l2-8H6"/><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/></svg> Tambah ke keranjang</button>
                                </form>
                            @else
                                <span class="inline-flex min-h-12 flex-1 items-center justify-center rounded-full bg-stone-100 px-5 text-sm font-semibold text-stone-500">Stok habis</span>
                            @endif

                            @if ($isFavorite)
                                <form method="POST" action="{{ route('user.favorites.destroy', $product) }}">@csrf @method('DELETE')<button type="submit" class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#f8e5eb] text-rose-600" aria-label="Hapus dari favorit"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m12 21-1.45-1.32C5.4 15.05 2 11.97 2 8.2 2 5.12 4.42 2.7 7.5 2.7c1.74 0 3.41.81 4.5 2.09a6.05 6.05 0 0 1 4.5-2.09c3.08 0 5.5 2.42 5.5 5.5 0 3.77-3.4 6.85-8.55 11.49L12 21Z"/></svg></button></form>
                            @else
                                <form method="POST" action="{{ route('user.favorites.store', $product) }}">@csrf<button type="submit" class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-line text-stone-500 transition hover:bg-[#f8e5eb] hover:text-rose-600" aria-label="Tambahkan ke favorit"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m12 21-1.45-1.32C5.4 15.05 2 11.97 2 8.2 2 5.12 4.42 2.7 7.5 2.7c1.74 0 3.41.81 4.5 2.09a6.05 6.05 0 0 1 4.5-2.09c3.08 0 5.5 2.42 5.5 5.5 0 3.77-3.4 6.85-8.55 11.49L12 21Z"/></svg></button></form>
                            @endif
                            @endguest
                        </div>
                    </section>
                </div>

                @if ($relatedProducts->isNotEmpty())
                    <div class="mt-14">
                        <div class="flex flex-wrap items-end justify-between gap-4">
                            <div><p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#a8942f]">Pilihan untuk Anda</p><h2 class="mt-2 font-display text-3xl font-semibold text-[#34873a]">Rekomendasi Bunga</h2></div>
                            <a href="{{ route('user.catalog.index') }}" class="text-sm font-semibold text-botanical-800 hover:underline">Semua buket →</a>
                        </div>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach ($relatedProducts as $relatedProduct)
                                <x-user.product-card :product="$relatedProduct" :is-favorite="in_array($relatedProduct->id_produk, $relatedFavoriteIds, true)" />
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif
@endsection
