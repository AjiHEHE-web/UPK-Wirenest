@extends('layouts.user')

@section('title', 'Buket bunga handmade WireNest')

@section('content')
    <section class="overflow-hidden bg-transparent">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-10 sm:px-6 sm:py-14 lg:grid-cols-[1fr_0.92fr] lg:px-8 lg:py-16">
            <div class="max-w-xl">
                <p class="inline-flex items-center gap-2 rounded-full bg-matcha/90 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#a8942f] shadow-sm">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Zm0 0c2-2 5-2 7 0-2 2-5 2-7 0Zm0 0c2 2 2 5 0 7-2-2-2-5 0-7Zm0 0c-2-2-5-2-7 0 2 2 5 2 7 0Z"/><circle cx="12" cy="12" r="1.4"/></svg>
                    Dirangkai tangan · Mekar selamanya
                </p>
                <h1 class="mt-5 max-w-xl font-display text-5xl font-semibold leading-[1.04] tracking-tight text-[#34873a] sm:text-6xl">Buket cantik,<br><span class="italic text-[#a8942f]">dirangkai dengan cinta.</span></h1>
                <p class="mt-5 max-w-lg text-base leading-7 text-stone-700 sm:text-lg">Bunga kawat bulu yang lembut dan penuh warna, dibuat satu
per satu untuk menemani momen yang ingin Anda kenang.</p>
                <a href="{{ route('user.catalog.index') }}" class="mt-7 inline-flex min-h-12 items-center justify-center gap-2 rounded-full bg-[#34873a] px-7 text-sm font-semibold text-white shadow-[0_8px_18px_rgba(65,95,72,.19)] transition hover:bg-botanical-800">
                    Belanja sekarang
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
                <div class="mt-9 grid max-w-lg grid-cols-3 divide-x divide-botanical-300/80 border-t border-botanical-300/80 pt-5 text-sm">
                    <div class="pr-3"><p class="font-semibold text-botanical-800">100%</p><p class="mt-1 text-xs leading-5 text-stone-600">Rangkai tangan</p></div>
                    <div class="px-3"><p class="font-semibold text-botanical-800">Tanpa</p><p class="mt-1 text-xs leading-5 text-stone-600">Perawatan khusus</p></div>
                    <div class="pl-3"><p class="font-semibold text-botanical-800">Unik</p><p class="mt-1 text-xs leading-5 text-stone-600">Dibuat sepenuh hati</p></div>
                </div>
            </div>

            <div class="relative isolate mx-auto aspect-square w-full max-w-[520px]">
                <div aria-hidden="true" class="absolute inset-[7%] rounded-full bg-gradient-to-br from-[#fffef3] via-[#f4f6ed] to-[#e8efe4] shadow-[0_22px_55px_rgba(64,91,65,.14)]"></div>
                <img src="{{ asset('images/hero-bouquet.png') }}" alt="Buket bunga chenille warna-warni WireNest" class="absolute inset-0 z-10 h-full w-full scale-[1.08] object-contain" loading="eager" fetchpriority="high" decoding="async">
            </div>
        </div>
    </section>

    @if ($categories->isNotEmpty())
        <section class="category-showcase bg-transparent px-4 pb-12 pt-2 sm:px-6 lg:px-8" aria-labelledby="category-showcase-title">
            <div class="mx-auto max-w-7xl">
                <div class="mb-6 text-center">
                    <h2 id="category-showcase-title" class="font-display text-3xl font-semibold text-[#171717] sm:text-4xl">Kategori Bunga</h2>
                    <p class="mt-1 text-sm text-[#8c8500] sm:text-base">Pilih jenis bunga favoritmu yang dirangkai teliti dengan bahan kawat bulu lembut.</p>
                </div>
                <div class="category-marquee" aria-label="Kategori bunga, arahkan kursor untuk menghentikan gerakan">
                    <div class="category-marquee-track">
                        @foreach ([false, true] as $duplicate)
                            <div @class(['category-marquee-group', 'category-marquee-group-copy' => $duplicate]) @if ($duplicate) aria-hidden="true" @endif>
                                @foreach ($categories->take(4) as $category)
                                    @php
                                        $categoryName = mb_strtolower($category->name);
                                        $categoryIcon = match (true) {
                                            str_contains($categoryName, 'lily') => '💐',
                                            str_contains($categoryName, 'tulip') => '🌷',
                                            str_contains($categoryName, 'mawar') => '🌹',
                                            str_contains($categoryName, 'matahari') => '🌻',
                                            default => '🌼',
                                        };
                                    @endphp
                                    <a href="{{ route('user.catalog.index', ['category' => $category->id_kategori]) }}" tabindex="{{ $duplicate ? '-1' : '0' }}" class="category-marquee-card">
                                        <span class="text-3xl" aria-hidden="true">{{ $categoryIcon }}</span>
                                        <span>{{ $category->name }}</span>
                                    </a>
                                @endforeach
                                <span class="category-marquee-spacer" aria-hidden="true"></span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="bg-transparent px-4 pb-16 pt-5 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#a8942f]">Koleksi WireNest</p>
                    <h2 class="mt-2 font-display text-3xl font-semibold text-[#34873a] sm:text-4xl">Buket pilihan</h2>
                </div>
                <a href="{{ route('user.catalog.index') }}" class="text-sm font-semibold text-botanical-800 transition hover:text-botanical-950">Lihat semua buket <span aria-hidden="true">→</span></a>
            </div>
            <div class="motion-stagger mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @forelse ($featuredProducts as $product)
                    <x-user.product-card :product="$product" :is-favorite="in_array($product->id_produk, $favoriteIds, true)" />
                @empty
                    <p class="col-span-full rounded-[26px] bg-matcha p-8 text-center text-sm text-stone-600">Buket pilihan akan segera hadir.</p>
                @endforelse
            </div>
        </div>
    </section>

@endsection
