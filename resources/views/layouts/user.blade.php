<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Toko Bunga') · WireNest</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body x-data="{ menuOpen: false, scrolled: window.scrollY > 24 }" x-on:scroll.window.passive="scrolled = window.scrollY > 24" x-on:keydown.escape.window="menuOpen = false" class="storefront min-h-screen bg-transparent text-ink">
        <a href="#main-content" class="sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:not-sr-only focus:rounded-lg focus:bg-matcha focus:px-4 focus:py-3 focus:text-ink">Lewati ke konten utama</a>
        @include('layouts.partials.storefront-navigation')

        @if (isset($landingToast))
            <div x-data="{ visible: true }" x-init="setTimeout(() => visible = false, 5000)" x-show="visible" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-3 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="-translate-y-2 opacity-0" role="status" class="fixed left-1/2 top-5 z-50 flex w-[calc(100%-2rem)] max-w-lg -translate-x-1/2 items-center gap-3 rounded-2xl border border-botanical-200 bg-matcha px-5 py-4 text-sm font-medium text-botanical-900 shadow-[0_16px_40px_rgba(28,40,30,.18)]">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-botanical-600 text-white" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg>
                </span>
                <span class="flex-1">{{ $landingToast }}</span>
                <button type="button" x-on:click="visible = false" class="grid h-9 w-9 shrink-0 place-items-center rounded-full text-botanical-800 transition hover:bg-botanical-100" aria-label="Tutup notifikasi">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </div>
        @endif

        <main id="main-content" class="motion-page-enter min-h-[65vh] bg-transparent">
            <div class="mx-auto max-w-7xl px-4 pt-5 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div role="status" class="flex items-start gap-3 rounded-2xl border border-botanical-200 bg-matcha px-4 py-3 text-sm text-botanical-800 shadow-sm">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div role="alert" class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-matcha px-4 py-3 text-sm text-rose-800 shadow-sm">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div role="alert" class="rounded-2xl border border-rose-200 bg-matcha px-4 py-3 text-sm text-rose-800 shadow-sm">
                        <p class="font-semibold">Periksa kembali data yang dimasukkan.</p>
                        <ul class="mt-2 list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            @yield('content')
        </main>

        <div data-cart-notice hidden role="status" aria-live="polite" class="fixed bottom-5 right-5 z-50 max-w-sm rounded-2xl border bg-matcha px-5 py-4 text-sm font-semibold shadow-xl">
            <span data-cart-notice-message></span>
        </div>

        <footer class="border-t border-line bg-matcha">
            <div class="mx-auto flex max-w-7xl flex-col gap-8 px-4 py-9 sm:px-6 lg:px-8">
                <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-center">
                    <div class="max-w-md">
                        <a href="{{ route('user.home') }}" class="inline-flex items-center gap-3 text-botanical-800">
                            <x-application-logo class="h-9 w-9 fill-current" />
                            <span class="font-display text-xl font-semibold">WireNest</span>
                        </a>
                        <p class="mt-2 text-sm leading-6 text-stone-600">Buket chenille buatan tangan, dirangkai dengan sabar untuk menemani momen yang berarti.</p>
                    </div>
                    <nav aria-label="Navigasi footer" class="flex flex-wrap gap-x-6 gap-y-3 text-sm font-medium text-stone-600">
                        <a href="{{ route('user.home') }}" class="transition hover:text-botanical-800">Beranda</a>
                        <a href="{{ route('user.catalog.index') }}" class="transition hover:text-botanical-800">Katalog</a>
                        <a href="{{ route('user.orders.index') }}" class="transition hover:text-botanical-800">Riwayat pesanan</a>
                    </nav>
                </div>
                <div class="flex flex-col justify-between gap-3 border-t border-line pt-5 text-xs text-stone-500 sm:flex-row sm:items-center">
                    <span>© {{ now()->year }} WireNest. Dibuat dengan perhatian, dirangkai untuk bertahan lama.</span>
                    <span class="inline-flex items-center gap-2 font-semibold tracking-wide text-[#a8942f]"><span aria-hidden="true">✦</span> Bunga kenang-kenangan yang abadi</span>
                </div>
            </div>
        </footer>
    </body>
</html>
