@props(['product'])

<article class="product-card group relative isolate min-h-[390px] overflow-hidden rounded-[26px] bg-botanical-900 shadow-[0_10px_28px_rgba(31,59,42,.16)] sm:min-h-[420px]">
    <a href="{{ route('user.catalog.show', $product) }}" class="absolute inset-0 flex flex-col justify-end overflow-hidden rounded-[inherit] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-botanical-700">
        @if ($product->image)
            <img src="{{ Storage::disk('public')->url($product->image) }}" alt="{{ $product->name_produk }}" class="product-card-photo absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out" loading="lazy" decoding="async">
        @else
            <div class="absolute inset-0 flex items-center justify-center text-white/50" aria-hidden="true">
                <svg class="h-20 w-20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Zm0 0c2-2 5-2 7 0-2 2-5 2-7 0Zm0 0c2 2 2 5 0 7-2 2-2 5 0 7Zm0 0c-2-2-5-2-7 0 2 2 5 2 7 0Z"/><circle cx="12" cy="12" r="1.4"/></svg>
            </div>
        @endif
        <span class="product-card-shade absolute inset-0 bg-gradient-to-t from-[#14271b]/95 via-[#14271b]/35 to-black/10 transition-colors duration-500" aria-hidden="true"></span>

        <span class="absolute left-4 top-4 rounded-full border border-white/30 bg-matcha/90 px-3 py-1.5 text-[11px] font-semibold tracking-wide text-[#a8942f] shadow-lg backdrop-blur-sm">Tersedia</span>
        <span class="absolute right-4 top-4 rounded-full border border-white/30 bg-matcha/90 px-3 py-1.5 text-[11px] font-semibold text-botanical-900 shadow-lg backdrop-blur-sm">Stok: {{ $product->stock_produk }}</span>

        <span class="product-card-copy relative block p-5 text-white sm:p-6">
            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-white/75">{{ $product->category?->name ?? 'Buket pilihan' }}</span>
            <span class="block font-display text-2xl font-semibold leading-tight sm:text-[26px]">{{ $product->name_produk }}</span>
            <span class="mt-2 block text-lg font-bold">Rp {{ number_format((float) $product->harga_produk, 0, ',', '.') }}</span>
            <span class="product-card-cta mt-4 inline-flex min-h-11 items-center gap-2 rounded-full border border-white/35 bg-matcha/15 px-5 text-sm font-semibold text-white shadow-lg backdrop-blur-md transition duration-300 ease-out">
                Lihat produk
                <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
            </span>
        </span>
    </a>
</article>
