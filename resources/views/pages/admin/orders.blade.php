@extends('layouts.admin')

@section('title')
    @switch($page)
        @case('index') Semua Pesanan @break
        @case('show') Detail Pesanan @break
    @endswitch
@endsection

@section('breadcrumb')
    @if ($page === 'index') Pesanan @else
        <a href="{{ route('admin.orders.index') }}" class="hover:text-botanical-800">Pesanan</a>
        <span aria-hidden="true">/</span>
        <span>Detail Pesanan</span>
    @endif
@endsection

@section('content')
@php
    $statusLabels = [
        'pending' => 'Pending',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
        'rejected' => 'Ditolak',
    ];
@endphp

@if ($page === 'index')
    <div>
        <p class="text-sm text-stone-500">Tinjau detail dan proses pesanan pelanggan sesuai tahapannya.</p>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-line bg-white shadow-sm">
        <form method="GET" action="{{ route('admin.orders.index') }}" data-ajax-search data-ajax-results="admin-orders-results" class="grid gap-3 border-b border-line p-4 sm:grid-cols-[1fr_220px_auto] sm:items-end sm:px-6">
            <div>
                <label for="order-search" class="mb-1.5 block text-xs font-semibold text-stone-600">Cari pesanan, pelanggan, atau produk</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-stone-400">@include('pages.admin.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])</span>
                    <input id="order-search" name="search" value="{{ $filters['search'] ?? '' }}" type="search" placeholder="ID pesanan, nama, username, email, atau produk..." class="h-11 w-full rounded-xl border-line bg-stone-50 pl-10 text-sm focus:border-botanical-500 focus:bg-white focus:ring-botanical-500">
                </div>
            </div>
            <div>
                <label for="order-status" class="mb-1.5 block text-xs font-semibold text-stone-600">Status pesanan</label>
                <select id="order-status" name="status" class="h-11 w-full rounded-xl border-line bg-white text-sm focus:border-botanical-500 focus:ring-botanical-500">
                    <option value="">Semua status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $statusLabels[$status] ?? ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-botanical-700 px-5 text-sm font-semibold text-white transition hover:bg-botanical-800">Terapkan</button>
        </form>

        <div id="admin-orders-results" data-ajax-results aria-live="polite">
        <div class="flex items-center justify-between px-5 py-4 sm:px-6">
            <div>
                <h2 class="text-base font-semibold text-ink">Daftar pesanan</h2>
                <p class="mt-1 text-sm text-stone-500">{{ $orders->total() }} pesanan ditemukan</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-left text-sm">
                <thead class="bg-stone-50/80 text-xs uppercase tracking-wide text-stone-500">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-semibold">ID Pesanan</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Pelanggan</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Tanggal</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Total</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Pesanan</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Pembayaran</th>
                        <th scope="col" class="px-6 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($orders as $order)
                        @php($latestPayment = $order->payments->sortByDesc('created_at')->first())
                        <tr class="transition hover:bg-stone-50/70">
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-ink">#{{ Str::limit($order->id_pesanan, 12, '') }}</td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-ink">{{ $order->user?->nama ?: $order->user?->name ?: 'Pelanggan' }}</p>
                                <p class="mt-0.5 text-xs text-stone-500">{{ $order->user?->email }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-stone-600">{{ $order->tanggal_pemesanan?->format('d M Y, H:i') ?? '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-ink">Rp {{ number_format((float) $order->total_harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-4"><x-admin.status-badge :status="$order->status_pesanan" /></td>
                            <td class="px-6 py-4">
                                @if ($latestPayment)
                                    <x-admin.status-badge :status="$latestPayment->status_pembayaran" />
                                @else
                                    <span class="text-xs text-stone-500">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg px-3 text-sm font-semibold text-botanical-700 transition hover:bg-botanical-50">
                                    @include('pages.admin.partials.icon', ['name' => 'eye', 'class' => 'h-4 w-4'])
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-14 text-center text-sm text-stone-500">Tidak ada pesanan yang sesuai filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($orders->hasPages())
            <div class="border-t border-line px-5 py-4 sm:px-6">{{ $orders->links() }}</div>
        @endif
        </div>
    </section>
@elseif ($page === 'show')
    <a href="{{ route('admin.orders.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg text-sm font-semibold text-stone-600 transition hover:text-botanical-700">
        @include('pages.admin.partials.icon', ['name' => 'back', 'class' => 'h-4 w-4'])
        Kembali ke pesanan
    </a>

    <div class="mt-4 grid gap-6 xl:grid-cols-[1fr_340px]">
        <section class="overflow-hidden rounded-2xl border border-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-5 sm:px-6">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-stone-500">ID Pesanan</p>
                    <h2 class="mt-1 break-all text-lg font-semibold text-ink">{{ $order->id_pesanan }}</h2>
                </div>
                <x-admin.status-badge :status="$order->status_pesanan" class="text-sm" />
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[540px] text-left text-sm">
                    <thead class="bg-stone-50/80 text-xs uppercase tracking-wide text-stone-500">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold">Produk</th>
                            <th scope="col" class="px-6 py-3 text-right font-semibold">Harga satuan</th>
                            <th scope="col" class="px-6 py-3 text-center font-semibold">Jumlah</th>
                            <th scope="col" class="px-6 py-3 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($order->items as $item)
                            <tr>
                                <td class="px-6 py-4 font-medium text-ink">{{ $item->product?->name_produk ?? 'Produk dihapus' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-stone-600">Rp {{ number_format((float) $item->harga_satuan, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center text-stone-600">{{ number_format($item->jumlah) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right font-semibold text-ink">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-sm text-stone-500">Detail produk tidak tersedia.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t border-line bg-stone-50/60">
                        <tr>
                            <th colspan="3" scope="row" class="px-6 py-4 text-right text-sm font-semibold text-ink">Total pesanan</th>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-base font-semibold text-botanical-800">Rp {{ number_format((float) $order->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <div class="space-y-6">
            <section class="rounded-2xl border border-line bg-white p-5 shadow-sm">
                <h2 class="text-base font-semibold text-ink">Informasi pelanggan</h2>
                <dl class="mt-4 space-y-4">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Nama</dt>
                        <dd class="mt-1 text-sm font-medium text-ink">{{ $order->user?->nama ?: $order->user?->name ?: 'Pelanggan' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Email</dt>
                        <dd class="mt-1 break-all text-sm text-ink">{{ $order->user?->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Tanggal pemesanan</dt>
                        <dd class="mt-1 text-sm text-ink">{{ $order->tanggal_pemesanan?->format('d M Y, H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Pembayaran</dt>
                        <dd class="mt-1 text-sm text-ink">{{ $hasApprovedPayment ? 'Bukti disetujui Admin' : 'Menunggu bukti atau verifikasi Admin' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-2xl border border-line bg-white p-5 shadow-sm">
                <h2 class="text-base font-semibold text-ink">Perbarui status</h2>
                @if ($nextStatuses !== [])
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="mt-4 space-y-3">
                        @csrf
                        @method('PATCH')
                        <label for="status_pesanan" class="block text-sm font-medium text-stone-600">Status selanjutnya</label>
                        <select id="status_pesanan" name="status_pesanan" required class="min-h-11 w-full rounded-xl border-line bg-white text-sm focus:border-botanical-500 focus:ring-botanical-500">
                            <option value="">Pilih status</option>
                            @foreach ($nextStatuses as $status)
                                <option value="{{ $status }}">{{ $statusLabels[$status] ?? ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        @error('status_pesanan') <p class="text-sm text-rose-700">{{ $message }}</p> @enderror
                        <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-botanical-700 px-4 text-sm font-semibold text-white transition hover:bg-botanical-800">
                            @include('pages.admin.partials.icon', ['name' => 'check', 'class' => 'h-4 w-4'])
                            Simpan Status
                        </button>
                    </form>
                @else
                    <p class="mt-3 rounded-xl bg-stone-50 p-3 text-sm leading-6 text-stone-600">
                        @if (! $hasApprovedPayment)
                            Status baru dapat diproses setelah user mengunggah bukti pembayaran dan Admin menyetujuinya.
                        @elseif ($order->status_pesanan === 'shipped')
                            Menunggu user mengonfirmasi bahwa barang telah diterima.
                        @else
                            Pesanan berada pada status akhir dan tidak dapat diubah lagi.
                        @endif
                    </p>
                @endif
            </section>
        </div>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-line bg-white shadow-sm">
        <div class="border-b border-line px-5 py-4 sm:px-6">
            <h2 class="text-base font-semibold text-ink">Pembayaran pesanan</h2>
        </div>
        <div class="divide-y divide-line">
            @forelse ($order->payments as $payment)
                <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 sm:px-6">
                    <div class="min-w-0">
                        <a href="{{ route('admin.transactions.show', $payment) }}" class="break-all text-sm font-semibold text-botanical-700 hover:text-botanical-900">{{ $payment->id_pembayaran }}</a>
                        <p class="mt-1 text-xs text-stone-500">{{ strtoupper($payment->metode_pembayaran) }} · {{ $payment->created_at?->format('d M Y, H:i') ?? '—' }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-semibold text-ink">Rp {{ number_format((float) $payment->jumlah, 0, ',', '.') }}</span>
                        <x-admin.status-badge :status="$payment->status_pembayaran" />
                    </div>
                </div>
            @empty
                <p class="px-6 py-7 text-sm text-stone-500">Belum ada pembayaran untuk pesanan ini.</p>
            @endforelse
        </div>
    </section>
@endif
@endsection
