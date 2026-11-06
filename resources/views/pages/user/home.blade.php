@extends('layouts.user')

@section('title', 'Bunga segar untuk momen istimewa')

@section('content')
    <section class="relative isolate overflow-hidden bg-cream">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-[1.05fr_0.95fr] lg:px-8 lg:py-24">
            <div class="relative z-10">
                <p class="inline-flex items-center gap-2 rounded-full border border-botanical-200 bg-white/80 px-4 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-botanical-800">
                    <span class="h-2 w-2 rounded-full bg-botanical-500"></span>
                    Dirangkai dengan sepenuh hati
                </p>
                <h1 class="mt-6 max-w-2xl text-4xl font-semibold leading-tight tracking-tight text-botanical-950 sm:text-5xl lg:text-6xl">Kirim bunga, sampaikan rasa yang tak terucap.</h1>
                <p class="mt-5 max-w-xl text-base leading-7 text-stone-600 sm:text-lg">Temukan rangkaian bunga pilihan untuk merayakan momen kecil maupun hari paling istimewa.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('user.catalog.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-full bg-botanical-800 px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-botanical-900">Jelajahi koleksi</a>
                    <a href="#kategori" class="inline-flex min-h-12 items-center justify-center rounded-full border border-botanical-200 bg-white px-6 text-sm font-semibold text-botanical-900 transition hover:bg-botanical-50">Lihat kategori</a>
                </div>
                <div class="mt-10 flex flex-wrap gap-x-8 gap-y-3 text-sm text-stone-600">
                    <span class="inline-flex items-center gap-2"><span class="text-botanical-700">✳</span> Dirangkai segar</span>
                    <span class="inline-flex items-center gap-2"><span class="text-botanical-700">✳</span> Pilihan penuh makna</span>
                </div>
            </div>
            <div class="relative mx-auto w-full max-w-xl">
                <div class="absolute -left-4 top-12 h-32 w-32 rounded-full bg-botanical-200/70 blur-3xl"></div>
                <div class="absolute -right-4 bottom-6 h-40 w-40 rounded-full bg-rose-100/80 blur-3xl"></div>
                <div class="relative aspect-[4/3] overflow-hidden rounded-[2rem] border-[10px] border-white bg-gradient-to-br from-botanical-200 via-cream to-rose-100 shadow-xl">
                    @if ($featuredProducts->first()?->image)
                        <img src="{{ Storage::disk('public')->url($featuredProducts->first()->image) }}" alt="{{ $featuredProducts->first()->name_produk }}" class="h-full w-full object-cover">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg viewBox="0 0 480 360" class="h-full w-full" aria-label="Ilustrasi rangkaian bunga" role="img">
                                <path d="M240 335c-12-66-8-118 0-175m0 120c-52-52-97-55-133-43 34 47 74 68 133 57m0-31c45-47 83-55 125-45-27 43-67 65-125 61" fill="none" stroke="#385f49" stroke-width="9" stroke-linecap="round"/>
                                <path d="M155 100c-33-5-48-34-33-61 27 1 43 23 33 61Zm85 13c-31-16-34-48-11-68 25 10 34 37 11 68Zm85-13c-10-33 7-59 35-61 15 27 0 55-35 61Zm-42 73c-13-31 1-59 28-66 18 24 7 54-28 66Z" fill="#e1ece1" stroke="#385f49" stroke-width="5"/>
                                <path d="M152 159c-25-3-37-25-25-45 21 1 33 18 25 45Zm59-15c-24-11-26-35-9-50 19 8 26 27 9 50Zm62 14c-8-25 5-44 26-46 11 20 0 41-26 46Z" fill="#f3d9d5" stroke="#8f625f" stroke-width="4"/>
                                <circle cx="155" cy="158" r="13" fill="#d3a56e"/><circle cx="211" cy="144" r="13" fill="#d3a56e"/><circle cx="273" cy="158" r="13" fill="#d3a56e"/>
                                <path d="m125 198 30 12 30-12-15 80h-30z" fill="#d7a982"/><path d="m140 205 15 12 15-12" fill="none" stroke="#fff9ef" stroke-width="5"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute bottom-4 left-4 rounded-2xl bg-white/90 px-4 py-3 shadow-sm backdrop-blur">
                        <p class="text-xs font-medium text-stone-500">Dirangkai untuk</p>
                        <p class="text-sm font-semibold text-botanical-900">Momen yang berarti</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="kategori" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-18 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-botanical-700">Temukan favoritmu</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-ink sm:text-3xl">Jelajahi berdasarkan kategori</h2>
            </div>
            <a href="{{ route('user.catalog.index') }}" class="text-sm font-semibold text-botanical-800 hover:text-botanical-950">Semua koleksi <span aria-hidden="true">→</span></a>
        </div>
        <div class="mt-7 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($categories as $category)
                <a href="{{ route('user.catalog.index', ['category' => $category->id_kategori]) }}" class="group rounded-2xl border border-line bg-white p-5 transition duration-200 hover:-translate-y-1 hover:border-botanical-200 hover:shadow-md">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-botanical-50 text-botanical-800">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Zm0 0c2-2 5-2 7 0-2 2-5 2-7 0Zm0 0c2 2 2 5 0 7-2-2-2-5 0-7Zm0 0c-2 2-5 2-7 0 2-2 5-2 7 0Z"/><circle cx="12" cy="12" r="1.4"/></svg>
                    </span>
                    <p class="mt-4 font-semibold text-ink group-hover:text-botanical-800">{{ $category->name }}</p>
                    <p class="mt-1 text-sm text-stone-500">{{ $category->available_products_count }} produk tersedia</p>
                </a>
            @empty
                <p class="col-span-full rounded-2xl bg-cream p-8 text-center text-sm text-stone-600">Koleksi kategori akan segera hadir.</p>
            @endforelse
        </div>
    </section>

    <section class="bg-cream/60">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-18 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-botanical-700">Pilihan untukmu</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-ink sm:text-3xl">Rangkaian bunga pilihan</h2>
                </div>
                <a href="{{ route('user.catalog.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-full border border-botanical-200 bg-white px-5 text-sm font-semibold text-botanical-900 transition hover:bg-botanical-50">Lihat semua</a>
            </div>
            <div class="mt-7 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @forelse ($featuredProducts as $product)
                    <article class="group overflow-hidden rounded-2xl border border-line bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                        <a href="{{ route('user.catalog.show', $product) }}" class="block">
                            <div class="aspect-[4/3] overflow-hidden bg-botanical-50">
                                @if ($product->image)
                                    <img src="{{ Storage::disk('public')->url($product->image) }}" alt="{{ $product->name_produk }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy">
                                @else
                                    <div class="flex h-full items-center justify-center text-botanical-300">
                                        <svg class="h-16 w-16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true"><path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Zm0 0c2-2 5-2 7 0-2 2-5 2-7 0Zm0 0c2 2 2 5 0 7-2-2-2-5 0-7Zm0 0c-2 2-5 2-7 0 2-2 5-2 7 0Z"/><circle cx="12" cy="12" r="1.4"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <p class="text-xs font-medium text-botanical-700">{{ $product->category->name }}</p>
                                <h3 class="mt-1 truncate font-semibold text-ink">{{ $product->name_produk }}</h3>
                                <p class="mt-2 font-semibold text-botanical-900">Rp {{ number_format((float) $product->harga_produk, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    </article>
                @empty
                    <p class="col-span-full rounded-2xl bg-white p-8 text-center text-sm text-stone-600">Produk pilihan akan segera hadir.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
