@extends('layouts.user')

@section('title', 'Checkout')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div>
            <p class="store-eyebrow">Langkah terakhir</p>
            <h1 class="store-title mt-2 text-4xl sm:text-5xl">Checkout</h1>
        </div>
        <form method="POST" action="{{ route('user.checkout.store') }}" class="mt-7 grid gap-6 lg:grid-cols-[1fr_380px]">
            @csrf
            <div class="space-y-6">
                <section class="store-card p-5 sm:p-6">
                    <h2 class="font-display text-xl font-medium text-botanical-950">Informasi pengiriman</h2>
                    <div class="mt-5">
                        <label for="alamat_pengiriman" class="mb-2 block text-sm font-semibold text-ink">Alamat lengkap <span class="text-rose-600">*</span></label>
                        <textarea id="alamat_pengiriman" name="alamat_pengiriman" rows="4" required maxlength="5000" class="w-full rounded-lg border-line bg-matcha text-sm focus:border-botanical-500 focus:ring-botanical-500" placeholder="Jalan, nomor rumah, kelurahan, kecamatan, kota, kode pos">{{ old('alamat_pengiriman', auth()->user()->alamat) }}</textarea>
                        @error('alamat_pengiriman') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                    </div>
                    <div class="mt-4">
                        <label for="catatan" class="mb-2 block text-sm font-semibold text-ink">Catatan untuk toko <span class="font-normal text-stone-500">(opsional)</span></label>
                        <textarea id="catatan" name="catatan" rows="3" maxlength="2000" class="w-full rounded-lg border-line bg-matcha text-sm focus:border-botanical-500 focus:ring-botanical-500" placeholder="Instruksi khusus untuk pesanan Anda">{{ old('catatan') }}</textarea>
                        @error('catatan') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                    </div>
                </section>
                <section class="rounded-2xl border border-white/80 bg-[#eff6f0] p-3.5 shadow-sm sm:p-4">
                    <h2 class="mb-2 text-[10px] font-normal text-[#45654e]">Pilih metode pembayaran yang akan digunakan</h2>
                    @php($selectedMethod = old('metode_pembayaran', 'dana'))
                    <div class="grid gap-1.5">
                        <details @if (in_array($selectedMethod, array_keys($walletMethods), true)) open @endif class="group rounded-xl bg-[#d7e9dc]">
                            <summary class="flex min-h-9 cursor-pointer list-none items-center justify-between gap-3 rounded-xl px-2.5 py-1 text-xs font-semibold text-[#244d3c] focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-botanical-600">
                                <span class="flex items-center gap-3"><svg aria-hidden="true" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 9h18M16 14h2" stroke-linecap="round"/></svg>E-Wallet</span>
                                <svg aria-hidden="true" class="h-4 w-4 transition-transform group-open:rotate-180" viewBox="0 0 20 20" fill="none"><path d="m5 7.5 5 5 5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </summary>
                            <div class="grid gap-1 px-2 pb-2">
                                @foreach ($walletMethods as $value => $label)
                                    <label class="flex min-h-9 cursor-pointer items-center justify-between gap-3 rounded-lg bg-white/70 px-3 py-1.5 text-xs text-ink transition hover:bg-white has-[:checked]:bg-white has-[:checked]:font-semibold">
                                        <span>{{ $label }}</span>
                                        <input type="radio" name="metode_pembayaran" value="{{ $value }}" @checked($selectedMethod === $value) required class="border-stone-300 text-botanical-700 focus:ring-botanical-500">
                                    </label>
                                @endforeach
                            </div>
                        </details>
                        <details @if (in_array($selectedMethod, array_keys($bankMethods), true)) open @endif class="group rounded-xl bg-[#d7e9dc]">
                            <summary class="flex min-h-9 cursor-pointer list-none items-center justify-between gap-3 rounded-xl px-2.5 py-1 text-xs font-semibold text-[#244d3c] focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-botanical-600">
                                <span class="flex items-center gap-3"><svg aria-hidden="true" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-5 9 5H3ZM5 19h14M4 21h16M7 10v8m5-8v8m5-8v8"/></svg>Bank</span>
                                <svg aria-hidden="true" class="h-4 w-4 transition-transform group-open:rotate-180" viewBox="0 0 20 20" fill="none"><path d="m5 7.5 5 5 5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </summary>
                            <div class="grid gap-1 px-2 pb-2">
                                @foreach ($bankMethods as $value => $label)
                                    <label class="flex min-h-9 cursor-pointer items-center justify-between gap-3 rounded-lg bg-white/70 px-3 py-1.5 text-xs text-ink transition hover:bg-white has-[:checked]:bg-white has-[:checked]:font-semibold">
                                        <span>{{ $label }}</span>
                                        <input type="radio" name="metode_pembayaran" value="{{ $value }}" @checked($selectedMethod === $value) required class="border-stone-300 text-botanical-700 focus:ring-botanical-500">
                                    </label>
                                @endforeach
                            </div>
                        </details>
                    </div>
                    @error('metode_pembayaran') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                    <p class="sr-only">Pesanan dibuat dengan status menunggu pembayaran. Unggah bukti transfer di halaman detail pesanan agar dapat diverifikasi.</p>
                </section>
            </div>
            <aside class="h-fit rounded-xl border border-botanical-200 bg-[#f3f1e8] p-5 sm:p-6">
                <h2 class="font-display text-xl font-medium text-botanical-950">Ringkasan belanja</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($items as $item)
                        <div class="flex justify-between gap-4 text-sm">
                            <span class="text-stone-600">{{ $item['product']->name_produk }} <span class="text-stone-400">× {{ $item['quantity'] }}</span></span>
                            <span class="whitespace-nowrap font-medium text-ink">Rp {{ number_format((float) $item['subtotal'], 2, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 flex justify-between border-t border-line pt-4">
                    <span class="font-semibold text-ink">Total</span>
                    <span class="font-semibold text-botanical-800">Rp {{ number_format((float) $total, 2, ',', '.') }}</span>
                </div>
                <button type="submit" class="store-action store-action-primary mt-5 min-h-12 w-full">Buat pesanan</button>
                <a href="{{ route('user.cart.index') }}" class="mt-3 inline-flex min-h-11 w-full items-center justify-center text-sm font-semibold text-stone-600 hover:text-botanical-800">Kembali ke keranjang</a>
            </aside>
        </form>
    </section>
@endsection
