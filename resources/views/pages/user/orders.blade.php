@extends('layouts.user')

@section('title', $page === 'index' ? 'Riwayat pesanan' : 'Detail pesanan')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Menunggu pembayaran',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'rejected' => 'Ditolak',
        ];
    @endphp

    @if ($page === 'index')
        <section class="bg-transparent px-4 pb-16 pt-8 sm:px-6 sm:pb-20 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="flex flex-wrap items-end justify-between gap-5">
                    <div>
                        <p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-botanical-700"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="5" width="16" height="16" rx="2"/><path d="M8 3v4m8-4v4M4 10h16"/></svg>Jurnal atelier pelanggan / pembelian</p>
                        <h1 class="mt-2 font-display text-4xl font-semibold text-[#34873a] sm:text-5xl">Riwayat pesanan</h1>
                        <p class="mt-2 max-w-xl text-sm leading-6 text-stone-600">Lihat perjalanan buket yang Anda pilih, dari dirangkai hingga tiba di rumah.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" data-ar-open-scanner class="inline-flex min-h-11 items-center justify-center gap-2 rounded-full bg-[#34873a] px-5 text-sm font-semibold text-white transition hover:bg-botanical-800">Scan QR AR</button>
                        <a href="{{ route('user.catalog.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-full border border-white bg-matcha px-5 text-sm font-semibold text-botanical-800 transition hover:bg-botanical-50">Belanja lagi</a>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
                    <nav id="user-order-status-filters" data-ajax-refresh aria-label="Filter status pesanan" class="flex max-w-full flex-wrap gap-2">
                        @foreach (['all' => 'Semua', 'pending' => $statusLabels['pending'], 'processing' => $statusLabels['processing'], 'shipped' => $statusLabels['shipped'], 'completed' => $statusLabels['completed'], 'cancelled' => $statusLabels['cancelled'], 'rejected' => $statusLabels['rejected']] as $status => $label)
                            <a href="{{ route('user.orders.index', array_filter(['search' => $filters['search'] ?? null, 'status' => $status === 'all' ? null : $status])) }}" @class(['inline-flex min-h-11 items-center rounded-full px-5 text-sm font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-700', 'bg-botanical-800 text-white shadow-sm' => ($filters['status'] ?? 'all') === $status, 'bg-white text-botanical-900 hover:bg-botanical-50' => ($filters['status'] ?? 'all') !== $status]) @if (($filters['status'] ?? 'all') === $status) aria-current="page" @endif>
                                {{ $label }}
                            </a>
                        @endforeach
                    </nav>
                    <span class="inline-flex items-center gap-2 text-xs text-stone-600"><svg class="h-4 w-4 text-botanical-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/></svg> Pengiriman standar: 2–3 hari kerja</span>
                </div>

                <form method="GET" action="{{ route('user.orders.index') }}" data-ajax-search data-ajax-results="user-orders-results" class="mt-5 flex min-h-14 w-full items-center gap-3 rounded-full bg-white px-5 transition focus-within:ring-2 focus-within:ring-botanical-200">
                    <label for="order-search" class="sr-only">Cari pesanan, produk, alamat, atau pembayaran</label>
                    <svg class="h-5 w-5 shrink-0 text-stone-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/></svg>
                    <input id="order-search" name="search" value="{{ $filters['search'] ?? '' }}" type="search" placeholder="Cari pesanan, produk, alamat..." class="h-12 min-w-0 flex-1 border-0 bg-transparent px-2 text-sm text-ink placeholder:text-stone-500 focus:ring-0">
                    @if (($filters['status'] ?? 'all') !== 'all')<input type="hidden" name="status" value="{{ $filters['status'] }}">@endif
                </form>

                <div id="user-orders-results" data-ajax-results aria-live="polite">
                <div class="motion-stagger mt-5 space-y-4">
                    @forelse ($orders as $order)
                        @php
                            $payment = $order->payments->sortByDesc('created_at')->first();
                            $firstItem = $order->items->first();
                        @endphp
                        <article class="rounded-[26px] border border-botanical-200 bg-white p-4 shadow-[0_12px_30px_rgba(28,73,33,.12)] sm:p-6">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-2 text-sm">
                                    <span class="rounded-full bg-[#fdf0f4] px-3 py-1.5 font-semibold text-botanical-800">#{{ strtoupper(substr($order->id_pesanan, 0, 8)) }}</span>
                                    <span class="text-[#d4a6b4]" aria-hidden="true">•</span>
                                    <span class="inline-flex items-center gap-1.5 text-xs text-stone-500"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="4" y="5" width="16" height="16" rx="2"/><path d="M8 3v4m8-4v4M4 10h16"/></svg>{{ $order->tanggal_pemesanan?->format('d M Y') ?? 'Tanggal tidak tersedia' }}</span>
                                </div>
                                <span @class(['inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold', 'bg-[#fff3c9] text-[#785e00]' => in_array($order->status_pesanan, ['pending', 'processing'], true), 'bg-[#e6f3e2] text-botanical-800' => in_array($order->status_pesanan, ['shipped', 'completed'], true), 'bg-[#f9e5ec] text-rose-800' => in_array($order->status_pesanan, ['cancelled', 'rejected'], true)])><span class="h-2 w-2 rounded-full bg-current"></span>{{ $statusLabels[$order->status_pesanan] ?? $order->status_pesanan }}</span>
                            </div>

                            <div class="mt-4 grid gap-4 md:grid-cols-[minmax(0,1fr)_160px_150px] md:items-center">
                                <div class="flex min-w-0 items-center gap-4">
                                    <div class="relative h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-botanical-50 sm:h-24 sm:w-24">
                                        @if ($firstItem?->product?->image)
                                            <img src="{{ Storage::disk('public')->url($firstItem->product->image) }}" alt="{{ $firstItem->product->name_produk }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                                        @else
                                            <div class="flex h-full items-center justify-center"><x-application-logo class="h-10 w-14" /></div>
                                        @endif
                                        @if ($order->items->count() > 1)<span class="absolute bottom-1 right-1 rounded-full bg-matcha/95 px-2 py-0.5 text-[10px] font-semibold text-stone-600">+{{ $order->items->count() - 1 }} lagi</span>@endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-botanical-700">{{ $firstItem?->product?->category?->name ?? 'Rangkaian WireNest' }}</p>
                                        <h2 class="mt-1 truncate font-display text-xl font-semibold text-[#34873a]">{{ $firstItem?->product?->name_produk ?? 'Detail pesanan WireNest' }}</h2>
                                        <p class="mt-1 text-sm text-stone-500">{{ $order->items->count() }} jenis buket dalam pesanan</p>
                                    </div>
                                </div>
                                <div class="md:text-right">
                                    <p class="text-xs text-stone-500">Total pesanan</p>
                                    <p class="mt-1 text-xl font-bold text-[#34873a]">Rp {{ number_format((float) $order->total_harga, 0, ',', '.') }}</p>
                                    @if ($payment)<p class="mt-1 text-xs text-stone-500">Pembayaran {{ ucfirst($payment->status_pembayaran) }}</p>@endif
                                </div>
                                <a href="{{ route('user.orders.show', $order) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-full bg-[#34873a] px-5 text-sm font-semibold text-white transition hover:bg-botanical-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-700">Lihat detail <span aria-hidden="true">→</span></a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-[26px] border border-botanical-200 bg-white px-6 py-16 text-center shadow-[0_12px_30px_rgba(28,73,33,.12)]">
                            <h2 class="font-display text-2xl font-semibold text-[#34873a]">Belum ada pesanan di sini</h2>
                            <p class="mt-2 text-sm text-stone-600">Pesanan Anda akan tampil di sini setelah checkout.</p>
                            <a href="{{ route('user.catalog.index') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-full bg-[#34873a] px-6 text-sm font-semibold text-white">Mulai belanja</a>
                        </div>
                    @endforelse
                </div>

                @if ($orders->hasPages())<div class="mt-7"><x-user.pagination :paginator="$orders" /></div>@endif
                </div>

                <div class="mt-10 flex flex-col items-start justify-between gap-5 rounded-[28px] bg-[#84a587] p-6 text-white sm:flex-row sm:items-center sm:px-8 sm:py-7">
                    <div class="flex items-center gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-matcha text-botanical-800"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m12 3 2.3 5.1L20 10l-4.1 4.1L17 20l-5-2.7L7 20l1.1-5.9L4 10l5.7-1.9L12 3Z"/><path d="m9 12 2 2 4-4"/></svg></span>
                        <div><h2 class="font-display text-xl font-semibold text-[#34873a]">Setiap batang dirangkai dengan sepenuh hati</h2><p class="mt-1 text-sm text-white">Buket personal dibuat dengan kesabaran dan keahlian tangan.</p></div>
                    </div>
                    <a href="{{ route('user.catalog.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-full bg-matcha px-6 text-sm font-semibold text-botanical-800 transition hover:bg-botanical-50">Jelajahi buket →</a>
                </div>
            </div>
        </section>

        @include('pages.ar.partials.scanner-dialog')
    @else
        <section @if ($order->status_pesanan === 'pending' && ! $order->payments->contains(fn ($payment) => filled($payment->image_bukti))) data-order-expiration-poll data-check-url="{{ route('user.orders.check-payment-deadline', $order) }}" @endif class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <a href="{{ route('user.orders.index') }}" class="text-sm font-semibold text-botanical-800 hover:underline">← Kembali ke riwayat</a>
            <div class="mt-5 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="store-eyebrow">Detail pesanan</p>
                    <p class="mt-2 text-sm text-stone-500">{{ $order->tanggal_pemesanan?->copy()->timezone(config('app.display_timezone'))->format('d M Y, H:i') ?? 'Tanggal tidak tersedia' }}</p>
                </div>
                <span data-order-status-pill @class(['rounded-full px-4 py-2 text-sm font-semibold capitalize', 'bg-botanical-50 text-botanical-800' => ! in_array($order->status_pesanan, ['cancelled', 'rejected'], true), 'bg-rose-50 text-rose-800' => in_array($order->status_pesanan, ['cancelled', 'rejected'], true)])>{{ $statusLabels[$order->status_pesanan] ?? $order->status_pesanan }}</span>
            </div>
            <p data-order-expired-notice hidden role="status" class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm leading-6 text-rose-800"></p>

            <div class="mt-7 grid gap-6 lg:grid-cols-[1fr_320px]">
                <div class="space-y-6">
                    <section class="overflow-hidden rounded-xl border border-line bg-matcha shadow-sm">
                        <h2 class="border-b border-line px-5 py-4 font-display text-xl font-medium text-botanical-950 sm:px-6">Produk pesanan</h2>
                        <div class="divide-y divide-line">
                            @foreach ($order->items as $item)
                                <div class="flex items-center justify-between gap-4 px-5 py-4 sm:px-6">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-botanical-50 text-botanical-300">
                                            @if ($item->product?->image)
                                                <img src="{{ Storage::disk('public')->url($item->product->image) }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async">
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
                        <div class="flex justify-between border-t border-line bg-[#f3f1e8] px-5 py-4 font-semibold sm:px-6">
                            <span>Total</span>
                            <span class="text-botanical-800">Rp {{ number_format((float) $order->total_harga, 2, ',', '.') }}</span>
                        </div>
                    </section>

                    <section class="rounded-xl border border-line bg-matcha p-5 shadow-sm sm:p-6">
                        <h2 class="font-display text-xl font-medium text-botanical-950">Alamat pengiriman</h2>
                        <p class="mt-3 whitespace-pre-line text-sm leading-6 text-stone-600">{{ $order->alamat_pengiriman ?: 'Alamat tidak tersedia.' }}</p>
                        @if ($order->catatan)
                            <p class="mt-4 border-t border-line pt-4 text-sm text-stone-600"><span class="font-semibold text-ink">Catatan:</span> {{ $order->catatan }}</p>
                        @endif
                    </section>

                    @if ($order->status_pesanan === 'pending' && ! $order->payments->contains(fn ($payment) => filled($payment->image_bukti)))
                        <div class="space-y-2">
                            <form data-order-cancel-form method="POST" action="{{ route('user.orders.cancel', $order) }}" onsubmit="return confirm('Batalkan pesanan ini?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-rose-200 px-5 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">Batalkan pesanan</button>
                            </form>
                            @error('order')
                                <p class="text-sm text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>
                    @elseif ($order->status_pesanan === 'shipped' && $order->payments->contains(fn ($payment) => $payment->status_pembayaran === 'paid' && $payment->bukti_pembayaran === 'accept' && ! empty($payment->image_bukti)))
                        <div class="space-y-2">
                            <form method="POST" action="{{ route('user.orders.confirm-received', $order) }}" onsubmit="return confirm('Konfirmasi bahwa barang sudah diterima?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-botanical-700 px-5 text-sm font-semibold text-white transition hover:bg-botanical-800">Pesanan diterima</button>
                            </form>
                            @error('order')
                                <p class="text-sm text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                <aside class="h-fit space-y-4">
                    @foreach ($order->payments as $payment)
                        <section class="rounded-xl border border-line bg-matcha p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <h2 class="font-display text-xl font-medium text-botanical-950">Pembayaran</h2>
                                <span data-payment-status-pill @class(['rounded-full px-3 py-1 text-xs font-semibold capitalize', 'bg-amber-50 text-amber-800' => $payment->status_pembayaran === 'pending', 'bg-botanical-50 text-botanical-800' => $payment->status_pembayaran === 'paid', 'bg-rose-50 text-rose-800' => $payment->status_pembayaran === 'failed'])>{{ $payment->status_pembayaran }}</span>
                            </div>
                            <dl class="mt-4 space-y-3 text-sm">
                                <div class="flex justify-between"><dt class="text-stone-500">Metode</dt><dd class="font-medium text-ink">{{ $walletMethods[$payment->metode_pembayaran]['label'] ?? strtoupper($payment->metode_pembayaran) }}</dd></div>
                                @if (isset($walletMethods[$payment->metode_pembayaran]['number']))
                                    <div class="flex justify-between gap-3"><dt class="text-stone-500">Nomor tujuan</dt><dd class="font-semibold text-ink">{{ $walletMethods[$payment->metode_pembayaran]['number'] }}</dd></div>
                                @endif
                                <div class="flex justify-between"><dt class="text-stone-500">Jumlah</dt><dd class="font-semibold text-ink">Rp {{ number_format((float) $payment->jumlah, 2, ',', '.') }}</dd></div>
                            </dl>
                            @if ($payment->image_bukti)
                                <a href="{{ route('user.orders.payment-proof.show', [$order, $payment]) }}" target="_blank" rel="noopener" class="mt-4 inline-flex min-h-10 items-center text-sm font-semibold text-botanical-800 hover:underline">Lihat bukti pembayaran</a>
                            @endif
                            @if ($payment->image_bukti === null && $payment->status_pembayaran === 'pending' && $order->status_pesanan === 'pending')
                                <form data-payment-proof-form method="POST" action="{{ route('user.orders.payment-proof', $order) }}" enctype="multipart/form-data" class="mt-5 rounded-lg border border-botanical-100 bg-[#f3f1e8] p-4">
                                    @csrf
                                    <input type="hidden" name="payment_id" value="{{ $payment->id_pembayaran }}">
                                    <label for="image-bukti-{{ $payment->id_pembayaran }}" class="mb-2 block text-sm font-semibold text-ink">Unggah bukti transfer</label>
                                    <input id="image-bukti-{{ $payment->id_pembayaran }}" name="image_bukti" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required class="block w-full text-xs text-stone-600 file:mr-3 file:min-h-10 file:rounded-lg file:border-0 file:bg-botanical-50 file:px-3 file:font-semibold file:text-botanical-800">
                                    <p class="mt-2 text-xs text-stone-500">JPG, PNG, atau WEBP, maksimal 5 MB.</p>
                                    <button type="submit" class="store-action store-action-primary mt-3 min-h-11 w-full">Kirim bukti</button>
                                </form>
                            @endif
                            @if ($payment->status_pembayaran === 'paid')
                                <p class="mt-4 border-t border-line pt-4 text-xs text-stone-500">Pembayaran diterima {{ $payment->paid_at?->copy()->timezone(config('app.display_timezone'))->format('d M Y, H:i') ?? '' }}</p>
                            @endif
                        </section>
                    @endforeach
                </aside>
            </div>
        </section>
    @endif
@endsection
