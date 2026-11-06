@extends('layouts.user')

@section('title', 'Favorit')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-botanical-700">Koleksi pribadi</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-ink">Produk favorit</h1>
            </div>
            <a href="{{ route('user.catalog.index') }}" class="text-sm font-semibold text-botanical-800 hover:underline">Jelajahi katalog →</a>
        </div>
        <div class="mt-7 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($favorites as $favorite)
                @if ($favorite->product && $favorite->product->stock_produk > 0)
                    @php($product = $favorite->product)
                    <article class="group overflow-hidden rounded-2xl border border-line bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <a href="{{ route('user.catalog.show', $product) }}" class="block">
                            <div class="aspect-[4/3] overflow-hidden bg-botanical-50">
                                @if ($product->image)
                                    <img src="{{ Storage::disk('public')->url($product->image) }}" alt="{{ $product->name_produk }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy">
                                @else
                                    <div class="flex h-full items-center justify-center text-botanical-300" aria-hidden="true">
                                        <svg class="h-14 w-14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Zm0 0c2-2 5-2 7 0-2 2-5 2-7 0Zm0 0c2 2 2 5 0 7-2-2-2-5 0-7Zm0 0c-2-2-5-2-7 0 2-2 5-2 7 0Z"/><circle cx="12" cy="12" r="1.4"/></svg>
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
                                <form method="POST" action="{{ route('user.favorites.destroy', $product) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-rose-600 hover:bg-rose-50" aria-label="Hapus {{ $product->name_produk }} dari favorit">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m12 21-1.45-1.32C5.4 15.05 2 11.97 2 8.2 2 5.12 4.42 2.7 7.5 2.7c1.74 0 3.41.81 4.5 2.09a6.05 6.05 0 0 1 4.5-2.09c3.08 0 5.5 2.42 5.5 5.5 0 3.77-3.4 6.85-8.55 11.49L12 21Z"/></svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('user.cart.store', $product) }}">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="inline-flex min-h-10 items-center rounded-full bg-botanical-800 px-4 text-xs font-semibold text-white hover:bg-botanical-900">Keranjang</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @else
                    <article class="rounded-2xl border border-line bg-white p-5">
                        <p class="font-semibold text-ink">{{ $favorite->product?->name_produk ?? 'Produk tidak tersedia' }}</p>
                        <p class="mt-2 text-sm text-stone-500">Produk ini sedang tidak tersedia.</p>
                        @if ($favorite->product)
                            <form method="POST" action="{{ route('user.favorites.destroy', $favorite->product) }}" class="mt-4">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-rose-700 hover:underline">Hapus dari favorit</button>
                            </form>
                        @endif
                    </article>
                @endif
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-botanical-200 bg-cream/50 px-6 py-16 text-center">
                    <h2 class="font-semibold text-ink">Belum ada produk favorit</h2>
                    <p class="mt-2 text-sm text-stone-600">Simpan rangkaian yang Anda sukai agar mudah ditemukan nanti.</p>
                    <a href="{{ route('user.catalog.index') }}" class="mt-5 inline-flex min-h-11 items-center rounded-full bg-botanical-800 px-5 text-sm font-semibold text-white hover:bg-botanical-900">Lihat katalog</a>
                </div>
            @endforelse
        </div>
        @if ($favorites->hasPages())
            <div class="mt-8">{{ $favorites->links() }}</div>
        @endif
    </section>
@endsection
