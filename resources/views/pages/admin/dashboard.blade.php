@extends('layouts.admin')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1.4fr_0.6fr]">
        <section class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-botanical-900 via-botanical-800 to-botanical-700 p-6 text-white shadow-sm sm:p-8">
            <div class="relative z-10 max-w-2xl">
                <p class="text-sm font-medium text-botanical-100/80">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                <h2 class="mt-3 text-2xl font-semibold tracking-tight sm:text-3xl">Selamat datang, {{ auth()->user()->nama ?: auth()->user()->name }}.</h2>
                <p class="mt-3 max-w-xl text-sm leading-6 text-white/75 sm:text-base">Pantau toko bunga Anda dan kelola pesanan dengan mudah dari satu tempat.</p>
            </div>
            <div aria-hidden="true" class="absolute -right-10 -top-20 h-72 w-72 rounded-full border border-white/10"></div>
            <div aria-hidden="true" class="absolute -right-2 -top-12 h-56 w-56 rounded-full border border-white/10"></div>
            <div aria-hidden="true" class="absolute -bottom-24 right-20 h-48 w-48 rounded-full bg-botanical-500/25 blur-2xl"></div>
            <div aria-hidden="true" class="absolute right-10 top-1/2 hidden -translate-y-1/2 text-white/10 sm:block">
                @include('pages.admin.partials.icon', ['name' => 'flower', 'class' => 'h-44 w-44'])
            </div>
        </section>

        <section class="grid grid-cols-2 gap-3 sm:gap-4" aria-label="Statistik toko">
            @php
                $statCards = [
                    ['label' => 'Total Produk', 'value' => number_format($stats['products']), 'icon' => 'package', 'hint' => 'Produk aktif di katalog'],
                    ['label' => 'Total Pesanan', 'value' => number_format($stats['orders']), 'icon' => 'orders', 'hint' => 'Semua status pesanan'],
                    ['label' => 'Total Transaksi', 'value' => number_format($stats['transactions']), 'icon' => 'credit-card', 'hint' => 'Seluruh pembayaran'],
                    ['label' => 'Pendapatan', 'value' => 'Rp '.number_format((float) $stats['revenue'], 0, ',', '.'), 'icon' => 'banknote', 'hint' => 'Dari pembayaran berhasil'],
                ];
            @endphp
            @foreach ($statCards as $stat)
                <article class="rounded-2xl border border-line bg-white p-4 shadow-sm sm:p-5">
                    <div class="flex items-center justify-between gap-2">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-botanical-50 text-botanical-700">
                            @include('pages.admin.partials.icon', ['name' => $stat['icon'], 'class' => 'h-5 w-5'])
                        </span>
                        <span class="hidden text-[11px] font-medium text-stone-400 sm:inline">{{ $stat['hint'] }}</span>
                    </div>
                    <p class="mt-4 text-xs font-medium text-stone-500 sm:text-sm">{{ $stat['label'] }}</p>
                    <p class="mt-1 break-words text-xl font-semibold tracking-tight text-ink sm:text-2xl">{{ $stat['value'] }}</p>
                </article>
            @endforeach
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
        <section class="rounded-2xl border border-line bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-ink">Pendapatan transaksi</h2>
                    <p class="mt-1 text-sm text-stone-500">Pendapatan per minggu bulan {{ now()->locale('id')->translatedFormat('F Y') }}</p>
                </div>
                <span class="rounded-full bg-botanical-50 px-3 py-1 text-xs font-semibold text-botanical-700">{{ now()->locale('id')->translatedFormat('F') }}</span>
            </div>
            <div class="mt-7 flex h-48 items-end gap-2 sm:gap-4" role="img" aria-label="Grafik pendapatan per minggu bulan {{ now()->locale('id')->translatedFormat('F Y') }}">
                @foreach ($revenueChart as $week)
                    @php
                        $height = $week['amount'] > 0 ? max(8, ($week['amount'] / $chartMaximum) * 100) : 4;
                    @endphp
                    <div class="flex h-full min-w-0 flex-1 flex-col items-center justify-end gap-2">
                        <span class="max-w-full truncate text-[10px] font-medium text-stone-500 sm:text-xs">
                            {{ $week['amount'] > 0 ? 'Rp '.number_format($week['amount'] / 1000, 0, ',', '.').'k' : '' }}
                        </span>
                        <div class="flex h-32 w-full items-end rounded-t-lg bg-botanical-50">
                            <div class="w-full rounded-t-lg bg-botanical-600 transition-all duration-300" style="height: {{ $height }}%" title="{{ $week['period'] }}: Rp {{ number_format($week['amount'], 0, ',', '.') }}"></div>
                        </div>
                        <span class="text-[11px] font-medium text-stone-500 sm:text-xs">{{ $week['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-line bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-ink">Ringkasan stok</h2>
                    <p class="mt-1 text-sm text-stone-500">Produk dengan stok paling sedikit</p>
                </div>
                <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-botanical-700 hover:text-botanical-900">Semua</a>
            </div>
            <div class="mt-5 divide-y divide-line">
                @forelse ($stockSummary as $product)
                    <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                        @if ($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="" class="h-11 w-11 rounded-xl object-cover" loading="lazy">
                        @else
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-botanical-50 text-botanical-600">
                                @include('pages.admin.partials.icon', ['name' => 'flower', 'class' => 'h-5 w-5'])
                            </span>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink">{{ $product->name_produk }}</p>
                            <p class="truncate text-xs text-stone-500">{{ $product->category->name }}</p>
                        </div>
                        <span @class([
                            'shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold',
                            'bg-rose-50 text-rose-700' => $product->stock_produk < 5,
                            'bg-amber-50 text-amber-800' => $product->stock_produk >= 5,
                        ])>{{ $product->stock_produk }} stok</span>
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-stone-500">Belum ada produk di katalog.</p>
                @endforelse
            </div>
        </section>
    </div>

@endsection
