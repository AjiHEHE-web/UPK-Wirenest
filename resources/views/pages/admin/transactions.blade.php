@extends('layouts.admin')

@section('title')
    @switch($page)
        @case('pending') Konfirmasi Pembayaran @break
        @case('history') Riwayat Transaksi @break
        @case('show') Detail Transaksi @break
        @case('receipt') Struk Pembayaran @break
    @endswitch
@endsection

@section('breadcrumb')
    @if ($page === 'pending') Konfirmasi Pembayaran
    @elseif ($page === 'history') Riwayat Transaksi
    @elseif ($page === 'show') <a href="{{ route('admin.transactions.history') }}" class="hover:text-botanical-800">Riwayat Transaksi</a> <span aria-hidden="true">/</span> <span>Detail Transaksi</span>
    @endif
@endsection

@section('content')
@if ($page === 'pending')
    <div>
        <p class="text-sm text-stone-500">Tinjau bukti transfer dan verifikasi pembayaran sebelum pesanan diproses.</p>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-line bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-4 sm:px-6">
            <div>
                <h2 class="text-base font-semibold text-ink">Pembayaran menunggu verifikasi</h2>
                <p class="mt-1 text-sm text-stone-500">{{ $payments->total() }} pembayaran menunggu pemeriksaan</p>
            </div>
            <a href="{{ route('admin.transactions.history') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3 text-sm font-semibold text-botanical-700 transition hover:bg-botanical-50">
                Riwayat transaksi
                @include('pages.admin.partials.icon', ['name' => 'arrow', 'class' => 'h-4 w-4'])
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1050px] text-left text-sm">
                <thead class="bg-stone-50/80 text-xs uppercase tracking-wide text-stone-500">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-semibold">Pembayaran</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Pesanan</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Pelanggan</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Metode</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Jumlah</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Bukti</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Status</th>
                        <th scope="col" class="px-6 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($payments as $payment)
                        <tr class="align-top transition hover:bg-stone-50/70">
                            <td class="whitespace-nowrap px-6 py-4">
                                <a href="{{ route('admin.transactions.show', $payment) }}" class="font-semibold text-botanical-700 hover:text-botanical-900">#{{ Str::limit($payment->id_pembayaran, 12, '') }}</a>
                                <p class="mt-1 text-xs text-stone-500">{{ $payment->created_at?->format('d M Y, H:i') ?? '—' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <a href="{{ route('admin.orders.show', $payment->order) }}" class="font-medium text-ink hover:text-botanical-700">#{{ Str::limit($payment->id_pesanan, 12, '') }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-ink">{{ $payment->order?->user?->nama ?: $payment->order?->user?->name ?: 'Pelanggan' }}</p>
                                <p class="mt-0.5 text-xs text-stone-500">{{ $payment->order?->user?->email }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-medium uppercase text-stone-600">{{ $payment->metode_pembayaran }}</td>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-ink">Rp {{ number_format((float) $payment->jumlah, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if ($payment->image_bukti)
                                    <a href="{{ route('admin.transactions.proof', $payment) }}" target="_blank" rel="noopener" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-line px-3 text-xs font-semibold text-botanical-700 transition hover:bg-botanical-50">
                                        @include('pages.admin.partials.icon', ['name' => 'eye', 'class' => 'h-4 w-4'])
                                        Lihat bukti
                                    </a>
                                @else
                                    <span class="text-xs text-amber-700">Belum tersedia</span>
                                @endif
                            </td>
                            <td class="px-6 py-4"><x-admin.status-badge :status="$payment->status_pembayaran" /></td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.transactions.verify', $payment) }}" x-data @submit.prevent="if (window.confirm('Terima pembayaran ini? Pesanan akan menunggu untuk diproses.')) $el.submit()">
                                        @csrf
                                        <input type="hidden" name="decision" value="accept">
                                        <button type="submit" @disabled(! $payment->image_bukti) class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-botanical-700 px-3 text-xs font-semibold text-white transition hover:bg-botanical-800 disabled:cursor-not-allowed disabled:opacity-40">Terima</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.transactions.verify', $payment) }}" x-data @submit.prevent="if (window.confirm('Tolak bukti pembayaran ini?')) $el.submit()">
                                        @csrf
                                        <input type="hidden" name="decision" value="decline">
                                        <button type="submit" @disabled(! $payment->image_bukti) class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-rose-200 px-3 text-xs font-semibold text-rose-700 transition hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-40">Tolak</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-botanical-50 text-botanical-700">@include('pages.admin.partials.icon', ['name' => 'check', 'class' => 'h-6 w-6'])</span>
                                <p class="mt-3 font-semibold text-ink">Semua pembayaran sudah ditangani</p>
                                <p class="mt-1 text-sm text-stone-500">Pembayaran baru akan muncul di halaman ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($payments->hasPages())
            <div class="border-t border-line px-5 py-4 sm:px-6">{{ $payments->links() }}</div>
        @endif
    </section>
@elseif ($page === 'history')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <p class="text-sm text-stone-500">Telusuri pembayaran yang telah diverifikasi dan cetak struk transaksi.</p>
        <a href="{{ route('admin.transactions.pending') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-line bg-white px-4 text-sm font-semibold text-ink transition hover:bg-stone-50">
            @include('pages.admin.partials.icon', ['name' => 'credit-card', 'class' => 'h-4 w-4'])
            Menunggu verifikasi
        </a>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-line bg-white shadow-sm">
        <form method="GET" action="{{ route('admin.transactions.history') }}" data-ajax-search data-ajax-results="admin-transactions-results" class="grid gap-3 border-b border-line p-4 sm:grid-cols-2 lg:grid-cols-[minmax(220px,1fr)_160px_160px_160px_auto] sm:px-6">
            <div>
                <label for="transaction-search" class="mb-1.5 block text-xs font-semibold text-stone-600">Cari transaksi</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-stone-400">@include('pages.admin.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])</span>
                    <input id="transaction-search" name="search" value="{{ $filters['search'] ?? '' }}" type="search" placeholder="ID transaksi, pesanan, pelanggan..." class="h-11 w-full rounded-xl border-line bg-stone-50 pl-10 text-sm focus:border-botanical-500 focus:bg-white focus:ring-botanical-500">
                </div>
            </div>
            <div>
                <label for="transaction-status" class="mb-1.5 block text-xs font-semibold text-stone-600">Status</label>
                <select id="transaction-status" name="status" class="h-11 w-full rounded-xl border-line bg-white text-sm focus:border-botanical-500 focus:ring-botanical-500">
                    <option value="">Semua</option>
                    @foreach (['paid' => 'Berhasil', 'failed' => 'Ditolak', 'refunded' => 'Dikembalikan'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date-from" class="mb-1.5 block text-xs font-semibold text-stone-600">Dari tanggal</label>
                <input id="date-from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" class="h-11 w-full rounded-xl border-line bg-white text-sm focus:border-botanical-500 focus:ring-botanical-500">
            </div>
            <div>
                <label for="date-to" class="mb-1.5 block text-xs font-semibold text-stone-600">Sampai tanggal</label>
                <input id="date-to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" class="h-11 w-full rounded-xl border-line bg-white text-sm focus:border-botanical-500 focus:ring-botanical-500">
            </div>
            <button type="submit" class="inline-flex min-h-11 items-center justify-center self-end rounded-xl bg-botanical-700 px-4 text-sm font-semibold text-white transition hover:bg-botanical-800">Terapkan</button>
        </form>

        <div id="admin-transactions-results" data-ajax-results aria-live="polite">
        <div class="flex items-center justify-between px-5 py-4 sm:px-6">
            <div>
                <h2 class="text-base font-semibold text-ink">Semua transaksi</h2>
                <p class="mt-1 text-sm text-stone-500">{{ $payments->total() }} transaksi ditemukan</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1050px] text-left text-sm">
                <thead class="bg-stone-50/80 text-xs uppercase tracking-wide text-stone-500">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-semibold">Pembayaran</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Pesanan</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Pelanggan</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Metode</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Jumlah</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Status</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Tanggal</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Verifikator</th>
                        <th scope="col" class="px-6 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($payments as $payment)
                        <tr class="transition hover:bg-stone-50/70">
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-ink">{{ Str::limit($payment->id_pembayaran, 12, '') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-stone-600">{{ Str::limit($payment->id_pesanan, 12, '') }}</td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-ink">{{ $payment->order?->user?->nama ?: $payment->order?->user?->name ?: 'Pelanggan' }}</p>
                                <p class="mt-0.5 text-xs text-stone-500">{{ $payment->order?->user?->email }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 uppercase text-stone-600">{{ $payment->metode_pembayaran }}</td>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-ink">Rp {{ number_format((float) $payment->jumlah, 0, ',', '.') }}</td>
                            <td class="px-6 py-4"><x-admin.status-badge :status="$payment->status_pembayaran" /></td>
                            <td class="whitespace-nowrap px-6 py-4 text-stone-600">{{ ($payment->paid_at ?? $payment->verified_at ?? $payment->created_at)?->format('d M Y') ?? '—' }}</td>
                            <td class="px-6 py-4 text-stone-600">{{ $payment->verifier?->nama ?: $payment->verifier?->name ?: '—' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.transactions.show', $payment) }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-stone-500 transition hover:bg-botanical-50 hover:text-botanical-700" aria-label="Detail transaksi {{ $payment->id_pembayaran }}">
                                        @include('pages.admin.partials.icon', ['name' => 'eye', 'class' => 'h-4 w-4'])
                                    </a>
                                    @if ($payment->status_pembayaran === 'paid')
                                        <a href="{{ route('admin.transactions.receipt', $payment) }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-stone-500 transition hover:bg-botanical-50 hover:text-botanical-700" aria-label="Cetak struk {{ $payment->id_pembayaran }}">
                                            @include('pages.admin.partials.icon', ['name' => 'banknote', 'class' => 'h-4 w-4'])
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-6 py-14 text-center text-sm text-stone-500">Belum ada transaksi yang cocok dengan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($payments->hasPages())
            <div class="border-t border-line px-5 py-4 sm:px-6">{{ $payments->links() }}</div>
        @endif
        </div>
    </section>
@elseif ($page === 'show')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ $payment->status_pembayaran === 'pending' ? route('admin.transactions.pending') : route('admin.transactions.history') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg text-sm font-semibold text-stone-600 transition hover:text-botanical-700">
            @include('pages.admin.partials.icon', ['name' => 'back', 'class' => 'h-4 w-4'])
            Kembali
        </a>
        @if ($payment->status_pembayaran === 'paid')
            <a href="{{ route('admin.transactions.receipt', $payment) }}" class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-botanical-700 px-4 text-sm font-semibold text-white transition hover:bg-botanical-800">
                @include('pages.admin.partials.icon', ['name' => 'banknote', 'class' => 'h-4 w-4'])
                Cetak struk
            </a>
        @endif
    </div>

    <div class="mt-4 grid gap-6 xl:grid-cols-[1fr_360px]">
        <section class="rounded-2xl border border-line bg-white p-5 shadow-sm sm:p-7">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-line pb-5">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-stone-500">ID Pembayaran</p>
                    <h2 class="mt-1 break-all text-lg font-semibold text-ink">{{ $payment->id_pembayaran }}</h2>
                </div>
                <x-admin.status-badge :status="$payment->status_pembayaran" class="text-sm" />
            </div>
            <dl class="grid gap-x-8 gap-y-5 border-b border-line py-6 sm:grid-cols-2">
                <div><dt class="text-xs font-medium uppercase tracking-wide text-stone-500">ID Pesanan</dt><dd class="mt-1 break-all text-sm font-semibold text-ink">{{ $payment->id_pesanan }}</dd></div>
                <div><dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Nama pelanggan</dt><dd class="mt-1 text-sm font-semibold text-ink">{{ $payment->order?->user?->nama ?: $payment->order?->user?->name ?: 'Pelanggan' }}</dd></div>
                <div><dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Metode pembayaran</dt><dd class="mt-1 text-sm font-semibold uppercase text-ink">{{ $payment->metode_pembayaran }}</dd></div>
                <div><dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Jumlah pembayaran</dt><dd class="mt-1 text-lg font-semibold text-botanical-800">Rp {{ number_format((float) $payment->jumlah, 0, ',', '.') }}</dd></div>
                <div><dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Tanggal transaksi</dt><dd class="mt-1 text-sm text-ink">{{ ($payment->paid_at ?? $payment->verified_at ?? $payment->created_at)?->format('d M Y, H:i') ?? '—' }}</dd></div>
                <div><dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Admin verifikator</dt><dd class="mt-1 text-sm text-ink">{{ $payment->verifier?->nama ?: $payment->verifier?->name ?: 'Belum diverifikasi' }}</dd></div>
            </dl>
            <div class="pt-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-sm font-semibold text-ink">Detail produk pesanan</h3>
                    @if ($payment->order)
                        <a href="{{ route('admin.orders.show', $payment->order) }}" class="text-sm font-semibold text-botanical-700 hover:text-botanical-900">Lihat pesanan</a>
                    @endif
                </div>
                <div class="mt-4 divide-y divide-line">
                    @forelse ($payment->order?->items ?? [] as $item)
                        <div class="flex flex-wrap items-center justify-between gap-3 py-3 first:pt-0">
                            <div>
                                <p class="text-sm font-medium text-ink">{{ $item->product?->name_produk ?? 'Produk dihapus' }}</p>
                                <p class="mt-1 text-xs text-stone-500">{{ $item->jumlah }} × Rp {{ number_format((float) $item->harga_satuan, 0, ',', '.') }}</p>
                            </div>
                            <p class="text-sm font-semibold text-ink">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <p class="py-5 text-sm text-stone-500">Detail produk tidak tersedia.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-line bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-ink">Bukti pembayaran</h2>
            @if ($payment->image_bukti)
                <a href="{{ route('admin.transactions.proof', $payment) }}" target="_blank" rel="noopener" class="mt-4 block overflow-hidden rounded-xl border border-line bg-stone-50">
                    <img src="{{ route('admin.transactions.proof', $payment) }}" alt="Bukti pembayaran untuk transaksi {{ $payment->id_pembayaran }}" class="max-h-[28rem] w-full object-contain">
                </a>
                <a href="{{ route('admin.transactions.proof', $payment) }}" target="_blank" rel="noopener" class="mt-3 inline-flex min-h-10 items-center gap-2 rounded-lg px-2 text-sm font-semibold text-botanical-700 hover:bg-botanical-50">
                    Buka gambar ukuran penuh
                    @include('pages.admin.partials.icon', ['name' => 'arrow', 'class' => 'h-4 w-4'])
                </a>
            @else
                <div class="mt-4 flex min-h-48 flex-col items-center justify-center rounded-xl border border-dashed border-stone-300 bg-stone-50 px-5 text-center">
                    @include('pages.admin.partials.icon', ['name' => 'eye', 'class' => 'h-8 w-8 text-stone-400'])
                    <p class="mt-3 text-sm font-semibold text-ink">Bukti belum tersedia</p>
                    <p class="mt-1 text-xs leading-5 text-stone-500">Verifikasi tidak dapat dilakukan sebelum bukti pembayaran diterima.</p>
                </div>
            @endif
        </section>
    </div>
@elseif ($page === 'receipt')
        <div class="no-print mx-auto mb-4 flex max-w-3xl justify-between gap-3">
            <a href="{{ route('admin.transactions.show', $payment) }}" class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-line bg-white px-4 text-sm font-semibold text-ink transition hover:bg-stone-50">
                @include('pages.admin.partials.icon', ['name' => 'back', 'class' => 'h-4 w-4'])
                Kembali
            </a>
            <button type="button" onclick="window.print()" class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-botanical-700 px-4 text-sm font-semibold text-white transition hover:bg-botanical-800">
                @include('pages.admin.partials.icon', ['name' => 'banknote', 'class' => 'h-4 w-4'])
                Cetak / Simpan PDF
            </button>
        </div>

        <main class="receipt mx-auto max-w-3xl rounded-2xl border border-line bg-white p-6 shadow-sm sm:p-10">
            <header class="border-b border-dashed border-stone-300 pb-6 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-botanical-50 text-botanical-700">
                    @include('pages.admin.partials.icon', ['name' => 'flower', 'class' => 'h-8 w-8'])
                </div>
                <h1 class="mt-3 text-2xl font-semibold tracking-tight text-ink">E-Commerce Bunga</h1>
                <p class="mt-1 text-sm text-stone-500">E-Commerce Bunga</p>
                <p class="mt-4 text-xs font-semibold uppercase tracking-[0.18em] text-botanical-700">Struk pembayaran</p>
            </header>

            <section class="grid gap-x-8 gap-y-4 border-b border-dashed border-stone-300 py-6 sm:grid-cols-2">
                <div><p class="text-xs text-stone-500">ID Pesanan</p><p class="mt-1 break-all text-sm font-semibold text-ink">{{ $payment->order?->id_pesanan }}</p></div>
                <div><p class="text-xs text-stone-500">ID Pembayaran</p><p class="mt-1 break-all text-sm font-semibold text-ink">{{ $payment->id_pembayaran }}</p></div>
                <div><p class="text-xs text-stone-500">Nama pelanggan</p><p class="mt-1 text-sm font-semibold text-ink">{{ $payment->order?->user?->nama ?: $payment->order?->user?->name ?: 'Pelanggan' }}</p></div>
                <div><p class="text-xs text-stone-500">Tanggal transaksi</p><p class="mt-1 text-sm font-semibold text-ink">{{ ($payment->paid_at ?? $payment->verified_at ?? $payment->created_at)?->format('d M Y, H:i') ?? '—' }}</p></div>
            </section>

            <section class="py-6">
                <h2 class="text-sm font-semibold text-ink">Rincian produk</h2>
                <table class="mt-4 w-full text-sm">
                    <thead>
                        <tr class="border-b border-line text-left text-xs uppercase tracking-wide text-stone-500">
                            <th scope="col" class="pb-3 font-semibold">Produk</th>
                            <th scope="col" class="pb-3 text-right font-semibold">Harga</th>
                            <th scope="col" class="pb-3 text-center font-semibold">Jumlah</th>
                            <th scope="col" class="pb-3 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($payment->order?->items ?? [] as $item)
                            <tr>
                                <td class="py-3 pr-2 font-medium text-ink">{{ $item->product?->name_produk ?? 'Produk dihapus' }}</td>
                                <td class="whitespace-nowrap py-3 text-right text-stone-600">Rp {{ number_format((float) $item->harga_satuan, 0, ',', '.') }}</td>
                                <td class="px-2 py-3 text-center text-stone-600">{{ number_format($item->jumlah) }}</td>
                                <td class="whitespace-nowrap py-3 text-right font-medium text-ink">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="space-y-3 border-t border-dashed border-stone-300 pt-5">
                <div class="flex justify-between gap-4 text-sm">
                    <span class="text-stone-600">Metode pembayaran</span>
                    <span class="font-semibold uppercase text-ink">{{ $payment->metode_pembayaran }}</span>
                </div>
                <div class="flex justify-between gap-4 text-sm">
                    <span class="text-stone-600">Status pembayaran</span>
                    <span class="font-semibold capitalize text-botanical-700">{{ $payment->status_pembayaran }}</span>
                </div>
                <div class="flex justify-between gap-4 border-t border-line pt-4 text-base">
                    <span class="font-semibold text-ink">Total</span>
                    <span class="font-semibold text-botanical-800">Rp {{ number_format((float) $payment->order?->total_harga, 0, ',', '.') }}</span>
                </div>
            </section>

            <footer class="mt-8 border-t border-dashed border-stone-300 pt-5 text-center">
                <p class="text-sm font-medium text-ink">Terima kasih telah berbelanja di E-Commerce Bunga.</p>
                <p class="mt-1 text-xs text-stone-500">Simpan struk ini sebagai bukti transaksi.</p>
            </footer>
        </main>
    
@endif
@endsection
