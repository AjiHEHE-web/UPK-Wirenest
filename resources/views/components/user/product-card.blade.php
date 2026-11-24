@props(['product', 'isFavorite' => false])

<article class="product-card flex h-full flex-col overflow-hidden rounded-[26px] border border-botanical-100 bg-white p-3.5 shadow-[0_10px_28px_rgba(31,59,42,.12)] transition-[transform,box-shadow,border-color] duration-200 ease-out">
    <div class="relative aspect-[1/0.92] overflow-hidden rounded-[18px] bg-botanical-50">
        <a href="{{ route('user.catalog.show', $product) }}" class="block h-full w-full focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-700" aria-label="Lihat {{ $product->name_produk }}">
            @if ($product->image)
                <img src="{{ Storage::disk('public')->url($product->image) }}" alt="{{ $product->name_produk }}" class="h-full w-full object-cover transition-transform duration-300 ease-out" loading="lazy" decoding="async">
            @else
                <div class="flex h-full items-center justify-center" aria-hidden="true">
                    <img src="{{ asset('images/wirenest-logo.png') }}" alt="" class="h-24 w-36 object-contain">
                </div>
            @endif
        </a>

        <span class="absolute left-2.5 top-2.5 rounded-full bg-[#fffdf6] px-3 py-1.5 text-[11px] font-semibold text-botanical-800 shadow-sm">Tersedia</span>
        @auth
            <div class="absolute right-2.5 top-2.5">
                @if ($isFavorite)
                    <form method="POST" action="{{ route('user.favorites.destroy', $product) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/95 text-rose-600 shadow-sm" aria-label="Hapus {{ $product->name_produk }} dari favorit">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m12 21-1.45-1.32C5.4 15.05 2 11.97 2 8.2 2 5.12 4.42 2.7 7.5 2.7c1.74 0 3.41.81 4.5 2.09a6.05 6.05 0 0 1 4.5-2.09c3.08 0 5.5 2.42 5.5 5.5 0 3.77-3.4 6.85-8.55 11.49L12 21Z"/></svg>
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('user.favorites.store', $product) }}">
                        @csrf
                        <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/95 text-stone-500 shadow-sm" aria-label="Tambahkan {{ $product->name_produk }} ke favorit">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m12 21-1.45-1.32C5.4 15.05 2 11.97 2 8.2 2 5.12 4.42 2.7 7.5 2.7c1.74 0 3.41.81 4.5 2.09a6.05 6.05 0 0 1 4.5-2.09c3.08 0 5.5 2.42 5.5 5.5 0 3.77-3.4 6.85-8.55 11.49L12 21Z"/></svg>
                        </button>
                    </form>
                @endif
            </div>
        @endauth
    </div>

    <div class="flex flex-1 flex-col px-0.5 pb-0.5 pt-4">
        <p class="text-[11px] font-medium uppercase tracking-[0.12em] text-botanical-700">{{ $product->category?->name ?? 'Buket pilihan' }}</p>
        <h2 class="mt-1 font-display text-lg font-semibold leading-snug text-ink">{{ $product->name_produk }}</h2>
        <p class="mt-1.5 line-clamp-2 min-h-10 text-[13px] leading-5 text-stone-600">{{ $product->deskripsi_produk ?: 'Buket chenille handmade yang dirangkai dengan penuh perhatian. Hadiah lembut dan tahan lama untuk momen yang berarti.' }}</p>

        <div class="mt-auto flex items-end justify-between gap-2 pt-3">
            <span class="text-xs text-stone-500">Harga</span>
            <span class="font-display text-xl font-semibold leading-tight text-botanical-900">Rp {{ number_format((float) $product->harga_produk, 0, ',', '.') }}</span>
        </div>

        <div class="mt-3 grid grid-cols-[0.9fr_1.1fr] gap-2">
            <a href="{{ route('user.catalog.show', $product) }}" class="inline-flex min-h-11 items-center justify-center gap-1.5 rounded-full bg-[#e6f0e8] px-3 text-sm font-semibold text-botanical-900 transition-colors hover:bg-[#d9e9dd] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                Detail
            </a>
            @auth
                <form method="POST" action="{{ route('user.cart.store', $product) }}" data-ajax-cart>
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-1.5 rounded-full bg-botanical-800 px-3 text-sm font-semibold text-white transition-colors hover:bg-botanical-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-700" aria-label="Tambah {{ $product->name_produk }} ke keranjang">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 4h2l2 12h11l2-8H6"/><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/><path d="M15 3v6m-3-3h6"/></svg>
                        Tambah
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center rounded-full bg-botanical-800 px-3 text-sm font-semibold text-white transition-colors hover:bg-botanical-900">Pesan</a>
            @endauth
        </div>
    </div>
</article>
