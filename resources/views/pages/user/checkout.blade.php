@extends('layouts.user')

@section('title', 'Checkout')

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div>
            <p class="text-sm font-semibold text-botanical-700">Langkah terakhir</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-ink">Checkout</h1>
        </div>
        <form method="POST" action="{{ route('user.checkout.store') }}" class="mt-7 grid gap-6 lg:grid-cols-[1fr_380px]">
            @csrf
            <div class="space-y-6">
                <section class="rounded-2xl border border-line bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-lg font-semibold text-ink">Informasi pengiriman</h2>
                    <div class="mt-5">
                        <label for="alamat_pengiriman" class="mb-2 block text-sm font-semibold text-ink">Alamat lengkap <span class="text-rose-600">*</span></label>
                        <textarea id="alamat_pengiriman" name="alamat_pengiriman" rows="4" required maxlength="5000" class="w-full rounded-xl border-line text-sm focus:border-botanical-500 focus:ring-botanical-500" placeholder="Jalan, nomor rumah, kelurahan, kecamatan, kota, kode pos">{{ old('alamat_pengiriman', auth()->user()->alamat) }}</textarea>
                        @error('alamat_pengiriman') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                    </div>
                    <div class="mt-4">
                        <label for="catatan" class="mb-2 block text-sm font-semibold text-ink">Catatan untuk toko <span class="font-normal text-stone-500">(opsional)</span></label>
                        <textarea id="catatan" name="catatan" rows="3" maxlength="2000" class="w-full rounded-xl border-line text-sm focus:border-botanical-500 focus:ring-botanical-500" placeholder="Instruksi khusus untuk pesanan Anda">{{ old('catatan') }}</textarea>
                        @error('catatan') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                    </div>
                </section>
                <section class="rounded-2xl border border-line bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-lg font-semibold text-ink">Metode pembayaran</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        @foreach ($methods as $value => $label)
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-4 transition has-[:checked]:border-botanical-500 has-[:checked]:bg-botanical-50">
                                <input type="radio" name="metode_pembayaran" value="{{ $value }}" @checked(old('metode_pembayaran', 'bank') === $value) required class="border-stone-300 text-botanical-700 focus:ring-botanical-500">
                                <span class="text-sm font-semibold text-ink">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('metode_pembayaran') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
                    <p class="mt-4 text-xs leading-5 text-stone-500">Pesanan dibuat dengan status menunggu pembayaran. Unggah bukti transfer di halaman detail pesanan agar dapat diverifikasi.</p>
                </section>
            </div>
            <aside class="h-fit rounded-2xl border border-line bg-cream/60 p-5 sm:p-6">
                <h2 class="text-lg font-semibold text-ink">Ringkasan belanja</h2>
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
                <button type="submit" class="mt-5 min-h-12 w-full rounded-xl bg-botanical-800 px-5 text-sm font-semibold text-white transition hover:bg-botanical-900">Buat pesanan</button>
                <a href="{{ route('user.cart.index') }}" class="mt-3 inline-flex min-h-10 w-full items-center justify-center text-sm font-semibold text-stone-600 hover:text-botanical-800">Kembali ke keranjang</a>
            </aside>
        </form>
    </section>
@endsection
