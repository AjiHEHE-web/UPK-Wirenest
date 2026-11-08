@extends('layouts.admin')

@section('title', 'Pengembalian Dana')

@section('breadcrumb', 'Keuangan / Pengembalian Dana')

@section('content')
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="max-w-3xl text-sm leading-6 text-stone-600">Transfer pengembalian dana dilakukan di luar aplikasi. Untuk pembayaran pending, periksa bukti transfer pelanggan terlebih dahulu dan pastikan dana benar-benar diterima sebelum mengirim pengembalian. Unggah bukti setelah transfer balik berhasil.</p>
        </div>
        <div class="flex gap-3 text-sm">
            <span class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 font-medium text-amber-800">{{ $pendingRefunds->total() }} menunggu</span>
            <span class="rounded-xl border border-botanical-200 bg-botanical-50 px-3 py-2 font-medium text-botanical-800">{{ $completedRefunds->total() }} selesai</span>
        </div>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-line bg-white shadow-sm">
        <div class="border-b border-line px-5 py-4 sm:px-6">
            <h2 class="text-base font-semibold text-ink">Pesanan menunggu pengembalian</h2>
            <p class="mt-1 text-sm text-stone-500">Pesanan dibatalkan, bukti transfer pelanggan tersedia, dan belum ada pengembalian dana yang tercatat.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1050px] text-left text-sm">
                <thead class="bg-stone-50/80 text-xs uppercase tracking-wide text-stone-500">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-semibold">Pembayaran / Pesanan</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Pelanggan</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Metode</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Status pembayaran</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Jumlah</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Bukti pembayaran</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Bukti pengembalian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($pendingRefunds as $payment)
                        <tr class="align-top transition hover:bg-stone-50/70">
                            <td class="whitespace-nowrap px-6 py-4">
                                <p class="font-semibold text-ink">#{{ Str::limit($payment->id_pembayaran, 12, '') }}</p>
                                <a href="{{ route('admin.orders.show', $payment->order) }}" class="mt-1 inline-block text-xs text-botanical-700 hover:text-botanical-900">Pesanan #{{ Str::limit($payment->id_pesanan, 12, '') }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-ink">{{ $payment->order?->user?->nama ?: $payment->order?->user?->name ?: 'Pelanggan' }}</p>
                                <p class="mt-0.5 text-xs text-stone-500">{{ $payment->order?->user?->email }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-medium uppercase text-stone-600">{{ $payment->metode_pembayaran }}</td>
                            <td class="px-6 py-4"><x-admin.status-badge :status="$payment->status_pembayaran" /></td>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-ink">Rp {{ number_format((float) $payment->jumlah, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.transactions.proof', $payment) }}" target="_blank" rel="noopener" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-line px-3 text-xs font-semibold text-botanical-700 transition hover:bg-botanical-50">
                                    @include('pages.admin.partials.icon', ['name' => 'eye', 'class' => 'h-4 w-4'])
                                    Lihat bukti
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.refunds.store', $payment) }}" enctype="multipart/form-data" class="flex min-w-64 flex-col items-start gap-2" x-data @submit.prevent="if (window.confirm('Pastikan transfer pengembalian dana telah berhasil. Lanjutkan pencatatan?')) $el.submit()">
                                    @csrf
                                    <label for="refund-proof-{{ $payment->id_pembayaran }}" class="sr-only">Bukti transfer pengembalian untuk pembayaran {{ $payment->id_pembayaran }}</label>
                                    <input id="refund-proof-{{ $payment->id_pembayaran }}" name="bukti_pengembalian" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" required class="block w-full text-xs text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-botanical-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-botanical-800 hover:file:bg-botanical-100">
                                    @error('bukti_pengembalian')
                                        <p class="text-xs text-rose-700">{{ $message }}</p>
                                    @enderror
                                    <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-botanical-700 px-3 text-xs font-semibold text-white transition hover:bg-botanical-800">
                                        @include('pages.admin.partials.icon', ['name' => 'check', 'class' => 'h-4 w-4'])
                                        Catat pengembalian
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-14 text-center">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-botanical-50 text-botanical-700">@include('pages.admin.partials.icon', ['name' => 'check', 'class' => 'h-6 w-6'])</span>
                                <p class="mt-3 font-semibold text-ink">Tidak ada pengembalian yang menunggu</p>
                                <p class="mt-1 text-sm text-stone-500">Pesanan dibatalkan yang sudah dibayar akan muncul di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($pendingRefunds->hasPages())
            <div class="border-t border-line px-5 py-4 sm:px-6">{{ $pendingRefunds->links() }}</div>
        @endif
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-line bg-white shadow-sm">
        <div class="border-b border-line px-5 py-4 sm:px-6">
            <h2 class="text-base font-semibold text-ink">Riwayat pengembalian</h2>
            <p class="mt-1 text-sm text-stone-500">{{ $completedRefunds->total() }} pengembalian telah dicatat</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[950px] text-left text-sm">
                <thead class="bg-stone-50/80 text-xs uppercase tracking-wide text-stone-500">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-semibold">Pembayaran / Pesanan</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Pelanggan</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Jumlah</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Tanggal</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Admin pemroses</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Bukti refund</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($completedRefunds as $refund)
                        <tr class="transition hover:bg-stone-50/70">
                            <td class="whitespace-nowrap px-6 py-4">
                                <p class="font-semibold text-ink">#{{ Str::limit($refund->id_pembayaran, 12, '') }}</p>
                                <p class="mt-1 text-xs text-stone-500">Pesanan #{{ Str::limit($refund->payment?->id_pesanan, 12, '') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-ink">{{ $refund->payment?->order?->user?->nama ?: $refund->payment?->order?->user?->name ?: 'Pelanggan' }}</p>
                                <p class="mt-0.5 text-xs text-stone-500">{{ $refund->payment?->order?->user?->email }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-ink">Rp {{ number_format((float) $refund->jumlah, 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-stone-600">{{ $refund->processed_at?->format('d M Y, H:i') ?? '—' }}</td>
                            <td class="px-6 py-4 text-stone-600">{{ $refund->processor?->nama ?: $refund->processor?->name ?: '—' }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.refunds.proof', $refund) }}" target="_blank" rel="noopener" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-line px-3 text-xs font-semibold text-botanical-700 transition hover:bg-botanical-50">
                                    @include('pages.admin.partials.icon', ['name' => 'eye', 'class' => 'h-4 w-4'])
                                    Lihat bukti
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-stone-500">Belum ada pengembalian dana yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($completedRefunds->hasPages())
            <div class="border-t border-line px-5 py-4 sm:px-6">{{ $completedRefunds->links() }}</div>
        @endif
    </section>
@endsection
