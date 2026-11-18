@extends('layouts.user')

@section('title', 'Favorit')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="store-eyebrow">Koleksi pribadi</p>
                <h1 class="store-title mt-2 text-4xl sm:text-5xl">Produk favorit</h1>
            </div>
            <a href="{{ route('user.catalog.index') }}" class="text-sm font-semibold text-botanical-800 transition hover:underline">Jelajahi katalog →</a>
        </div>
        <div class="motion-stagger mt-7 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($favorites as $favorite)
                @if ($favorite->product && $favorite->product->stock_produk > 0)
                    @php($product = $favorite->product)
                    <x-user.product-card :product="$product" :is-favorite="true" />
                @else
                    <article class="rounded-2xl border border-line bg-matcha p-5">
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
                    <a href="{{ route('user.catalog.index') }}" class="store-action store-action-primary mt-5">Lihat katalog</a>
                </div>
            @endforelse
        </div>
        @if ($favorites->hasPages())
            <div class="mt-8">{{ $favorites->links() }}</div>
        @endif
    </section>
@endsection
