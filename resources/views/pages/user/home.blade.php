@extends('layouts.user')

@section('title', 'Buket bunga handmade WireNest')

@section('content')
    @php($heroProduct = $featuredProducts->first())

    <section class="overflow-hidden bg-transparent">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-10 sm:px-6 sm:py-14 lg:grid-cols-[1fr_0.92fr] lg:px-8 lg:py-16">
            <div class="max-w-xl">
                <p class="inline-flex items-center gap-2 rounded-full bg-matcha/90 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#a8942f] shadow-sm">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Zm0 0c2-2 5-2 7 0-2 2-5 2-7 0Zm0 0c2 2 2 5 0 7-2-2-2-5 0-7Zm0 0c-2-2-5-2-7 0 2 2 5 2 7 0Z"/><circle cx="12" cy="12" r="1.4"/></svg>
                    Dirangkai tangan · Mekar selamanya
                </p>
                <h1 class="mt-5 max-w-xl font-display text-5xl font-semibold leading-[1.04] tracking-tight text-[#34873a] sm:text-6xl">Buket cantik,<br><span class="italic text-[#a8942f]">dirangkai dengan cinta.</span></h1>
                <p class="mt-5 max-w-lg text-base leading-7 text-stone-700 sm:text-lg">Bunga chenille yang lembut dan penuh warna, dibuat satu per satu untuk menemani momen yang ingin Anda kenang.</p>
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

            @if ($heroProduct)
                <div class="relative mx-auto w-full max-w-[520px] rounded-[34px] bg-matcha p-3.5 shadow-[0_22px_55px_rgba(64,91,65,.14)] sm:p-4">
                    <div class="relative aspect-[1.06/1] overflow-hidden rounded-[25px] bg-botanical-100">
                        @if ($heroProduct->image)
                            <img src="{{ Storage::disk('public')->url($heroProduct->image) }}" alt="{{ $heroProduct->name_produk }}" class="h-full w-full object-cover" loading="eager" fetchpriority="high" decoding="async">
                        @else
                            <div class="flex h-full items-center justify-center text-botanical-300"><x-application-logo class="h-36 w-36 fill-current" /></div>
                        @endif
                        <span class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full bg-matcha/95 px-4 py-2 text-xs font-semibold text-ink shadow-sm"><span class="h-2 w-2 rounded-full bg-botanical-600"></span> Rangkaian pilihan</span>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-3 px-2 pb-1 pt-4">
                        <div class="min-w-0">
                            <h2 class="truncate font-semibold text-ink">{{ $heroProduct->name_produk }}</h2>
                            <p class="mt-0.5 text-sm text-stone-500">Buket chenille handmade</p>
                        </div>
                        <span class="rounded-full bg-[#e2f4dd] px-4 py-2 text-xs font-semibold text-botanical-800">Buatan tangan</span>
                    </div>
                </div>
            @else
                <div class="mx-auto flex aspect-square w-full max-w-[520px] items-center justify-center rounded-[34px] bg-matcha text-botanical-300 shadow-lg"><x-application-logo class="h-40 w-40 fill-current" /></div>
            @endif
        </div>
    </section>

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
                    <x-user.product-card :product="$product" />
                @empty
                    <p class="col-span-full rounded-[26px] bg-matcha p-8 text-center text-sm text-stone-600">Buket pilihan akan segera hadir.</p>
                @endforelse
            </div>
        </div>
    </section>

    @if ($categories->isNotEmpty())
        <section class="bg-transparent px-4 pb-14 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl rounded-[28px] bg-matcha/75 p-5 sm:p-7">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-botanical-700">Temukan favorit Anda</p>
                        <h2 class="mt-1 font-display text-2xl font-semibold text-[#34873a]">Jelajahi kategori</h2>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($categories as $category)
                            <a href="{{ route('user.catalog.index', ['category' => $category->id_kategori]) }}" class="inline-flex min-h-10 items-center rounded-full border border-botanical-200 bg-matcha px-4 text-sm font-medium text-botanical-800 transition hover:bg-botanical-50">{{ $category->name }}</a>
                        @endforeach
                        <a href="{{ route('user.catalog.index') }}" class="inline-flex min-h-10 items-center rounded-full bg-botanical-700 px-4 text-sm font-semibold text-white transition hover:bg-botanical-800">Semua kategori <span class="ml-1" aria-hidden="true">→</span></a>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
