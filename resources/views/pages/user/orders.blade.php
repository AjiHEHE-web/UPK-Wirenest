@extends('layouts.user')

@section('title', $page === 'index' ? 'Riwayat pesanan' : 'Detail pesanan')

@section('content')
    @if ($page === 'index')
        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-botanical-700">Semua momen istimewa</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-ink">Riwayat pesanan</h1>
                </div>
                <a href="{{ route('user.catalog.index') }}" class="text-sm font-semibold text-botanical-800 hover:underline">Belanja lagi →</a>
            </div>
            <form method="GET" action="{{ route('user.orders.index') }}" class="mt-6 flex flex-col gap-3 sm:flex-row">
                <label for="order-search" class="sr-only">Cari berdasarkan ID pesanan</label>
                <input id="order-search" name="search" value="{{ $filters['search'] ?? '' }}" type="search" placeholder="Cari ID pesanan..." class="h-11 w-full rounded-xl border-line bg-white text-sm focus:border-botanical-500 focus:ring-botanical-500 sm:max-w-md">
                <button type="submit" class="min-h-11 rounded-xl bg-botanical-800 px-5 text-sm font-semibold text-white hover:bg-botanical-900">Cari pesanan</button>
            </form>
            <div class="mt-6 space-y-4">
                @forelse ($orders as $order)
                    @php($payment = $order->payments->sortByDesc('created_at')->first())
                    <article class="rounded-2xl border border-line bg-white p-5 shadow-sm sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-stone-500">ID Pesanan</p>
                                <p class="mt-1 break-all font-semibold text-ink">{{ $order->id_pesanan }}</p>
                                <p class="mt-2 text-sm text-stone-500">{{ $order->tanggal_pemesanan?->format('d M Y, H:i') ?? 'Tanggal tidak tersedia' }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="rounded-full bg-botanical-50 px-3 py-1 text-xs font-semibold capitalize text-botanical-800">{{ $order->status_pesanan }}</span>
                                @if ($payment)
                                    <span @class(['rounded-full px-3 py-1 text-xs font-semibold capitalize', 'bg-amber-50 text-amber-800' => $payment->status_pembayaran === 'pending', 'bg-botanical-50 text-botanical-800' => $payment->status_pembayaran === 'paid', 'bg-rose-50 text-rose-800' => in_array($payment->status_pembayaran, ['failed', 'refunded'], true)])>{{ $payment->status_pembayaran }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-5 flex flex-wrap items-end justify-between gap-4 border-t border-line pt-4">
                            <div>
                                <p class="text-xs text-stone-500">Total pesanan</p>
                                <p class="mt-1 font-semibold text-botanical-800">Rp {{ number_format((float) $order->total_harga, 2, ',', '.') }}</p>
                            </div>
                            <a href="{{ route('user.orders.show', $order) }}" class="inline-flex min-h-10 items-center rounded-xl border border-line px-4 text-sm font-semibold text-ink hover:bg-cream">Lihat detail</a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-botanical-200 bg-cream/50 px-6 py-16 text-center">
                        <h2 class="font-semibold text-ink">Belum ada pesanan</h2>
                        <p class="mt-2 text-sm text-stone-600">Pesanan Anda akan tampil di sini setelah checkout.</p>
                        <a href="{{ route('user.catalog.index') }}" class="mt-5 inline-flex min-h-11 items-center rounded-full bg-botanical-800 px-5 text-sm font-semibold text-white hover:bg-botanical-900">Mulai belanja</a>
                    </div>
                @endforelse
            </div>
            @if ($orders->hasPages())
                <div class="mt-8">{{ $orders->links() }}</div>
            @endif
        </section>
    @else
        <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <a href="{{ route('user.orders.index') }}" class="text-sm font-semibold text-botanical-800 hover:underline">← Kembali ke riwayat</a>
            <div class="mt-5 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-botanical-700">Detail pesanan</p>
                    <h1 class="mt-2 break-all text-2xl font-semibold tracking-tight text-ink sm:text-3xl">{{ $order->id_pesanan }}</h1>
                    <p class="mt-2 text-sm text-stone-500">{{ $order->tanggal_pemesanan?->format('d M Y, H:i') ?? 'Tanggal tidak tersedia' }}</p>
                </div>
                <span class="rounded-full bg-botanical-50 px-4 py-2 text-sm font-semibold capitalize text-botanical-800">{{ $order->status_pesanan }}</span>
            </div>

            <div class="mt-7 grid gap-6 lg:grid-cols-[1fr_320px]">
                <div class="space-y-6">
                    <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-sm">
                        <h2 class="border-b border-line px-5 py-4 font-semibold text-ink sm:px-6">Produk pesanan</h2>
                        <div class="divide-y divide-line">
                            @foreach ($order->items as $item)
                                <div class="flex items-center justify-between gap-4 px-5 py-4 sm:px-6">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-botanical-50 text-botanical-300">
                                            @if ($item->product?->image)
                                                <img src="{{ Storage::disk('public')->url($item->product->image) }}" alt="" class="h-full w-full object-cover">
                                            @else
                                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Zm0 0c2-2 5-2 7 0-2 2-5 2-7 0Zm0 0c2 2 2 5 0 7-2-2-2-5 0-7Zm0 0c-2-2-5-2-7 0 2-2 5-2 7 0Z"/><circle cx="12" cy="12" r="1.4"/></svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink">{{ $item->product?->name_produk ?? 'Produk tidak tersedia' }}</p>
                                            <p class="mt-1 text-xs text-stone-500">Rp {{ number_format((float) $item->harga_satuan, 2, ',', '.') }} × {{ $item->jumlah }}</p>
                                        </div>
                                    </div>
                                    <p class="whitespace-nowrap text-sm font-semibold text-ink">Rp {{ number_format((float) $item->subtotal, 2, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex justify-between border-t border-line bg-cream/40 px-5 py-4 font-semibold sm:px-6">
                            <span>Total</span>
                            <span class="text-botanical-800">Rp {{ number_format((float) $order->total_harga, 2, ',', '.') }}</span>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-line bg-white p-5 shadow-sm sm:p-6">
                        <h2 class="font-semibold text-ink">Alamat pengiriman</h2>
                        <p class="mt-3 whitespace-pre-line text-sm leading-6 text-stone-600">{{ $order->alamat_pengiriman ?: 'Alamat tidak tersedia.' }}</p>
                        @if ($order->catatan)
                            <p class="mt-4 border-t border-line pt-4 text-sm text-stone-600"><span class="font-semibold text-ink">Catatan:</span> {{ $order->catatan }}</p>
                        @endif
                    </section>

                    @if ($order->status_pesanan === 'pending' && $order->payments->every(fn ($payment) => $payment->status_pembayaran !== 'paid'))
                        <form method="POST" action="{{ route('user.orders.cancel', $order) }}" onsubmit="return confirm('Batalkan pesanan ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="min-h-11 rounded-xl border border-rose-200 px-5 text-sm font-semibold text-rose-700 hover:bg-rose-50">Batalkan pesanan</button>
                        </form>
                    @endif
                </div>

                <aside class="h-fit space-y-4">
                    @foreach ($order->payments as $payment)
                        <section class="rounded-2xl border border-line bg-white p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <h2 class="font-semibold text-ink">Pembayaran</h2>
                                <span @class(['rounded-full px-3 py-1 text-xs font-semibold capitalize', 'bg-amber-50 text-amber-800' => $payment->status_pembayaran === 'pending', 'bg-botanical-50 text-botanical-800' => $payment->status_pembayaran === 'paid', 'bg-rose-50 text-rose-800' => in_array($payment->status_pembayaran, ['failed', 'refunded'], true)])>{{ $payment->status_pembayaran }}</span>
                            </div>
                            <dl class="mt-4 space-y-3 text-sm">
                                <div class="flex justify-between gap-3"><dt class="text-stone-500">ID Pembayaran</dt><dd class="break-all text-right font-medium text-ink">{{ $payment->id_pembayaran }}</dd></div>
                                <div class="flex justify-between"><dt class="text-stone-500">Metode</dt><dd class="font-medium uppercase text-ink">{{ $payment->metode_pembayaran }}</dd></div>
                                <div class="flex justify-between"><dt class="text-stone-500">Jumlah</dt><dd class="font-semibold text-ink">Rp {{ number_format((float) $payment->jumlah, 2, ',', '.') }}</dd></div>
                            </dl>
                            @if ($payment->image_bukti)
                                <a href="{{ route('user.orders.payment-proof.show', [$order, $payment]) }}" target="_blank" rel="noopener" class="mt-4 inline-flex min-h-10 items-center text-sm font-semibold text-botanical-800 hover:underline">Lihat bukti pembayaran</a>
                            @endif
                            @if ($payment->status_pembayaran === 'pending' && $order->status_pesanan === 'pending')
                                <form method="POST" action="{{ route('user.orders.payment-proof', $order) }}" enctype="multipart/form-data" class="mt-4 border-t border-line pt-4">
                                    @csrf
                                    <input type="hidden" name="payment_id" value="{{ $payment->id_pembayaran }}">
                                    <label for="image-bukti-{{ $payment->id_pembayaran }}" class="mb-2 block text-sm font-semibold text-ink">Unggah bukti transfer</label>
                                    <input id="image-bukti-{{ $payment->id_pembayaran }}" name="image_bukti" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required class="block w-full text-xs text-stone-600 file:mr-3 file:min-h-10 file:rounded-lg file:border-0 file:bg-botanical-50 file:px-3 file:font-semibold file:text-botanical-800">
                                    <p class="mt-2 text-xs text-stone-500">JPG, PNG, atau WEBP, maksimal 5 MB.</p>
                                    <button type="submit" class="mt-3 min-h-10 w-full rounded-xl bg-botanical-800 px-4 text-sm font-semibold text-white hover:bg-botanical-900">Kirim bukti</button>
                                </form>
                            @endif
                            @if ($payment->status_pembayaran === 'paid')
                                <p class="mt-4 border-t border-line pt-4 text-xs text-stone-500">Pembayaran diterima {{ $payment->paid_at?->format('d M Y, H:i') ?? '' }}</p>
                            @endif
                        </section>
                    @endforeach
                </aside>
            </div>
        </section>
    @endif
@endsection
