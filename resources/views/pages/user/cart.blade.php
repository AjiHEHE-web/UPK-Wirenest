@extends('layouts.user')

@section('title', 'Keranjang')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div>
            <p class="text-sm font-semibold text-botanical-700">Hampir jadi milikmu</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-ink">Keranjang belanja</h1>
        </div>
        @if ($items->isEmpty())
            <div class="mt-7 rounded-3xl border border-dashed border-botanical-200 bg-cream/50 px-6 py-16 text-center">
                <h2 class="text-xl font-semibold text-ink">Keranjang Anda masih kosong</h2>
                <p class="mt-2 text-sm text-stone-600">Temukan buket dan rangkaian bunga favorit untuk memulai pesanan.</p>
                <a href="{{ route('user.catalog.index') }}" class="mt-5 inline-flex min-h-11 items-center rounded-full bg-botanical-800 px-5 text-sm font-semibold text-white hover:bg-botanical-900">Jelajahi katalog</a>
            </div>
        @else
            <div class="mt-7 grid gap-6 lg:grid-cols-[1fr_340px]">
                <div class="space-y-4">
                    @foreach ($items as $item)
                        <article class="flex flex-col gap-4 rounded-2xl border border-line bg-white p-4 shadow-sm sm:flex-row sm:items-center">
                            <a href="{{ route('user.catalog.show', $item['product']) }}" class="h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-botanical-50">
                                @if ($item['product']->image)
                                    <img src="{{ Storage::disk('public')->url($item['product']->image) }}" alt="{{ $item['product']->name_produk }}" class="h-full w-full object-cover">
                                @else
                                    <span class="flex h-full items-center justify-center text-botanical-300" aria-hidden="true">
                                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Zm0 0c2-2 5-2 7 0-2 2-5 2-7 0Zm0 0c2 2 2 5 0 7-2-2-2-5 0-7Zm0 0c-2-2-5-2-7 0 2 2 5 2 7 0Z"/><circle cx="12" cy="12" r="1.4"/></svg>
                                    </span>
                                @endif
                            </a>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-stone-500">{{ $item['product']->category?->name ?? 'Rangkaian bunga' }}</p>
                                <h2 class="mt-1 truncate font-semibold text-ink">{{ $item['product']->name_produk }}</h2>
                                <p class="mt-1 text-sm font-medium text-botanical-800">Rp {{ number_format((float) $item['unit_price'], 2, ',', '.') }} / item</p>
                                @if ($item['quantity'] > $item['product']->stock_produk)
                                    <p class="mt-2 text-xs font-medium text-rose-700">Stok saat ini hanya {{ $item['product']->stock_produk }}.</p>
                                @endif
                            </div>
                            <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end">
                                <form method="POST" action="{{ route('user.cart.update', $item['product']) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <label for="quantity-{{ $item['product']->id_produk }}" class="sr-only">Jumlah {{ $item['product']->name_produk }}</label>
                                    <input id="quantity-{{ $item['product']->id_produk }}" name="quantity" type="number" min="1" max="99" value="{{ $item['quantity'] }}" class="h-10 w-20 rounded-lg border-line text-center text-sm focus:border-botanical-500 focus:ring-botanical-500">
                                    <button type="submit" class="text-xs font-semibold text-botanical-800 hover:underline">Perbarui</button>
                                </form>
                                <p class="font-semibold text-ink">Rp {{ number_format((float) $item['subtotal'], 2, ',', '.') }}</p>
                            </div>
                            <form method="POST" action="{{ route('user.cart.destroy', $item['product']) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-stone-500 hover:bg-rose-50 hover:text-rose-700" aria-label="Hapus {{ $item['product']->name_produk }} dari keranjang">×</button>
                            </form>
                        </article>
                    @endforeach
                    <form method="POST" action="{{ route('user.cart.clear') }}" class="flex justify-end">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-medium text-stone-500 hover:text-rose-700">Kosongkan keranjang</button>
                    </form>
                </div>
                <aside class="h-fit rounded-2xl border border-line bg-cream/60 p-5 sm:p-6">
                    <h2 class="text-lg font-semibold text-ink">Ringkasan pesanan</h2>
                    <div class="mt-5 flex items-center justify-between border-t border-line pt-4 text-sm">
                        <span class="text-stone-600">Subtotal</span>
                        <span class="font-semibold text-ink">Rp {{ number_format((float) $total, 2, ',', '.') }}</span>
                    </div>
                    <p class="mt-3 text-xs leading-5 text-stone-500">Total akhir dihitung kembali berdasarkan harga dan stok terbaru saat checkout.</p>
                    <a href="{{ route('user.checkout.create') }}" class="mt-5 inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-botanical-800 px-5 text-sm font-semibold text-white transition hover:bg-botanical-900">Lanjut ke checkout</a>
                    <a href="{{ route('user.catalog.index') }}" class="mt-3 inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-line bg-white px-5 text-sm font-semibold text-ink hover:bg-stone-50">Lanjut belanja</a>
                </aside>
            </div>
        @endif
    </section>
@endsection
