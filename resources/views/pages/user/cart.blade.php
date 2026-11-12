@extends('layouts.user')

@section('title', 'Keranjang belanja')

@section('content')
    <section class="bg-transparent px-4 pb-16 pt-9 sm:px-6 sm:pb-20 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#a8942f]">Buket pilihan Anda</p>
                    <h1 class="mt-2 font-display text-4xl font-semibold text-[#34873a] sm:text-5xl">Keranjang belanja</h1>
                </div>
                @if ($items->isNotEmpty())
                    <p class="text-sm text-stone-600">Meninjau <span class="font-semibold text-botanical-800">{{ $items->sum('quantity') }} buket</span></p>
                @endif
            </div>

            @if ($items->isEmpty())
                <div class="mt-8 rounded-[28px] bg-matcha px-6 py-16 text-center shadow-sm">
                    <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-botanical-50 text-botanical-700"><svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M3 4h2l2 12h11l2-8H6"/><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/></svg></span>
                    <h2 class="mt-4 font-display text-2xl font-semibold text-[#34873a]">Keranjang masih kosong</h2>
                    <p class="mt-2 text-sm text-stone-600">Temukan buket favorit untuk memulai pesanan.</p>
                    <a href="{{ route('user.catalog.index') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-full bg-[#34873a] px-6 text-sm font-semibold text-white transition hover:bg-botanical-800">Jelajahi katalog</a>
                </div>
            @else
                <div class="mt-7 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_360px] lg:gap-8">
                    <section class="overflow-hidden rounded-[26px] bg-matcha shadow-[0_10px_28px_rgba(47,75,55,.08)]" aria-label="Daftar keranjang">
                        <div class="hidden grid-cols-[minmax(0,1fr)_115px_180px_125px_42px] gap-4 bg-[#83a487] px-6 py-3 text-xs font-semibold uppercase tracking-wide text-white md:grid">
                            <span>Buket & variasi</span><span class="text-right">Harga</span><span class="text-center">Jumlah</span><span class="text-right">Subtotal</span><span></span>
                        </div>
                        <div class="divide-y divide-line">
                            @foreach ($items as $item)
                                <article class="relative grid gap-4 px-4 py-5 sm:px-6 md:grid-cols-[minmax(0,1fr)_115px_180px_125px_42px] md:items-center">
                                    <div class="flex min-w-0 items-center gap-4">
                                        <a href="{{ route('user.catalog.show', $item['product']) }}" class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-botanical-50 sm:h-24 sm:w-24">
                                            @if ($item['product']->image)
                                                <img src="{{ Storage::disk('public')->url($item['product']->image) }}" alt="{{ $item['product']->name_produk }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                                            @else
                                                <span class="flex h-full items-center justify-center text-botanical-300"><x-application-logo class="h-9 w-9 fill-current" /></span>
                                            @endif
                                        </a>
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-botanical-700">{{ $item['product']->category?->name ?? 'Koleksi buket' }}</p>
                                            <h2 class="mt-1 truncate font-display text-lg font-semibold text-[#34873a] sm:text-xl">{{ $item['product']->name_produk }}</h2>
                                            @if ($item['quantity'] > $item['product']->stock_produk)
                                                <p class="mt-1 text-xs font-medium text-rose-700">Stok saat ini hanya {{ $item['product']->stock_produk }}.</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between text-sm md:block md:text-right">
                                        <span class="text-xs text-stone-500 md:hidden">Harga satuan</span>
                                        <span class="font-medium text-stone-700">Rp {{ number_format((float) $item['unit_price'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between md:justify-center">
                                        <span class="text-xs text-stone-500 md:hidden">Jumlah</span>
                                        <form method="POST" action="{{ route('user.cart.update', $item['product']) }}" class="flex items-center gap-1 rounded-full bg-[#fff8c9] p-1">
                                            @csrf
                                            @method('PATCH')
                                            <label for="quantity-{{ $item['product']->id_produk }}" class="sr-only">Jumlah {{ $item['product']->name_produk }}</label>
                                            <input id="quantity-{{ $item['product']->id_produk }}" name="quantity" type="number" min="1" max="99" value="{{ $item['quantity'] }}" class="h-9 w-14 rounded-full border-0 bg-transparent p-0 text-center text-sm font-semibold text-ink focus:ring-2 focus:ring-botanical-500">
                                            <button type="submit" class="inline-flex min-h-9 items-center rounded-full px-2 text-[11px] font-semibold text-botanical-800 transition hover:bg-matcha" aria-label="Perbarui jumlah">Perbarui</button>
                                        </form>
                                    </div>
                                    <div class="flex items-center justify-between text-sm md:block md:text-right">
                                        <span class="text-xs text-stone-500 md:hidden">Subtotal</span>
                                        <span class="font-bold text-[#34873a]">Rp {{ number_format((float) $item['subtotal'], 0, ',', '.') }}</span>
                                    </div>
                                    <form method="POST" action="{{ route('user.cart.destroy', $item['product']) }}" class="absolute right-4 top-5 md:static md:justify-self-end">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-stone-400 transition hover:bg-rose-50 hover:text-rose-700" aria-label="Hapus {{ $item['product']->name_produk }} dari keranjang"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
                                    </form>
                                </article>
                            @endforeach
                        </div>
                        <div class="flex justify-end border-t border-line px-5 py-4 sm:px-6">
                            <form method="POST" action="{{ route('user.cart.clear') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-stone-500 transition hover:text-rose-700">Kosongkan keranjang</button>
                            </form>
                        </div>
                    </section>

                    <aside class="h-fit rounded-[26px] bg-matcha p-6 shadow-[0_10px_28px_rgba(47,75,55,.08)] sm:p-7">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="font-display text-2xl font-semibold text-[#34873a]">Ringkasan pesanan</h2>
                            <svg class="h-5 w-5 text-botanical-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
                        </div>
                        <div class="mt-5 space-y-4 text-sm">
                            <div class="flex justify-between gap-3"><span class="text-stone-600">Subtotal</span><span class="font-semibold text-ink">Rp {{ number_format((float) $total, 0, ',', '.') }}</span></div>
                            <div class="flex justify-between gap-3"><span class="text-stone-600">Pengiriman</span><span class="rounded-full bg-[#fff8c9] px-2 py-0.5 text-xs font-semibold text-[#a8942f]">Dihitung saat checkout</span></div>
                        </div>
                        <div class="mt-5 rounded-2xl bg-[#dff1d9] px-4 py-4">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-botanical-800">Total sementara</p>
                            <p class="mt-1 text-2xl font-bold text-[#34873a]">Rp {{ number_format((float) $total, 0, ',', '.') }}</p>
                        </div>
                        <p class="mt-3 text-xs leading-5 text-stone-500">Total akhir dihitung kembali berdasarkan harga, stok, dan pengiriman saat checkout.</p>
                        <a href="{{ route('user.checkout.create') }}" class="mt-5 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-full bg-[#34873a] px-5 text-sm font-semibold text-white shadow-[0_6px_16px_rgba(65,95,72,.18)] transition hover:bg-botanical-800">Lanjut ke checkout <span aria-hidden="true">→</span></a>
                        <a href="{{ route('user.catalog.index') }}" class="mt-3 inline-flex min-h-11 w-full items-center justify-center text-sm font-semibold text-stone-600 transition hover:text-botanical-800">← Lanjut belanja</a>
                        <div class="mt-5 flex justify-center gap-5 border-t border-line pt-4 text-[11px] font-medium text-stone-500"><span>✓ Dikemas aman</span><span>✦ Tahan lama</span></div>
                    </aside>
                </div>
            @endif
        </div>
    </section>
@endsection
