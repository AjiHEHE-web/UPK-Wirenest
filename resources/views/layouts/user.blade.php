<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Toko Bunga') · E-Commerce Bunga</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body x-data="{ menuOpen: false }" x-on:keydown.escape.window="menuOpen = false" class="min-h-screen bg-white text-ink">
        <a href="#main-content" class="sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:not-sr-only focus:rounded-lg focus:bg-white focus:px-4 focus:py-3 focus:text-ink">Lewati ke konten utama</a>
        <header class="sticky top-0 z-30 border-b border-line bg-white/95 backdrop-blur">
            <nav class="mx-auto flex min-h-20 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8" aria-label="Navigasi utama">
                <a href="{{ route('user.home') }}" class="flex shrink-0 items-center gap-3" aria-label="E-Commerce Bunga, beranda">
                    <span class="flex h-11 w-11 items-center justify-center rounded-full bg-botanical-100 text-botanical-800">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Zm0 0c2-2 5-2 7 0-2 2-5 2-7 0Zm0 0c2 2 2 5 0 7-2-2-2-5 0-7Zm0 0c-2 2-5 2-7 0 2-2 5-2 7 0Z"/><circle cx="12" cy="12" r="1.4"/></svg>
                    </span>
                    <span>
                        <span class="block font-semibold tracking-tight text-botanical-900">Bunga</span>
                        <span class="block text-[11px] font-medium tracking-[0.18em] text-stone-500">FLOWER SHOP</span>
                    </span>
                </a>

                <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-line text-ink lg:hidden" aria-label="Buka menu" aria-expanded="false" x-on:click="menuOpen = !menuOpen" x-bind:aria-expanded="menuOpen.toString()">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="hidden items-center gap-7 lg:flex">
                    <a href="{{ route('user.home') }}" @class(['text-sm font-semibold text-botanical-800' => request()->routeIs('user.home'), 'text-sm font-medium text-stone-600 transition hover:text-botanical-800' => ! request()->routeIs('user.home')])>Beranda</a>
                    <a href="{{ route('user.catalog.index') }}" @class(['text-sm font-semibold text-botanical-800' => request()->routeIs('user.catalog.*'), 'text-sm font-medium text-stone-600 transition hover:text-botanical-800' => ! request()->routeIs('user.catalog.*')])>Katalog</a>
                    <a href="{{ route('user.favorites.index') }}" class="text-sm font-medium text-stone-600 transition hover:text-botanical-800">Favorit</a>
                    <a href="{{ route('user.orders.index') }}" class="text-sm font-medium text-stone-600 transition hover:text-botanical-800">Pesanan</a>
                </div>

                <div class="hidden items-center gap-3 lg:flex">
                    <a href="{{ route('user.cart.index') }}" class="inline-flex min-h-11 items-center gap-2 rounded-full border border-line px-4 text-sm font-semibold text-ink transition hover:border-botanical-300 hover:bg-botanical-50">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 4h2l2 12h11l2-8H6"/><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/></svg>
                        Keranjang
                    </a>
                    <a href="{{ route('user.profile.show') }}" class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-botanical-100 text-sm font-semibold text-botanical-800" aria-label="Profil {{ auth()->user()->nama }}">
                        @if (auth()->user()->profil_user)
                            <img src="{{ Storage::disk('public')->url(auth()->user()->profil_user) }}" alt="" class="h-full w-full object-cover">
                        @else
                            {{ mb_strtoupper(mb_substr(auth()->user()->nama ?: auth()->user()->name, 0, 1)) }}
                        @endif
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="min-h-11 rounded-full px-3 text-sm font-medium text-stone-600 transition hover:bg-stone-50 hover:text-botanical-800">Keluar</button>
                    </form>
                </div>
            </nav>

            <div x-cloak x-show="menuOpen" x-transition class="border-t border-line bg-white px-4 py-4 lg:hidden">
                <div class="mx-auto flex max-w-7xl flex-col gap-1">
                    <a href="{{ route('user.home') }}" class="rounded-xl px-4 py-3 text-sm font-semibold text-botanical-800 hover:bg-botanical-50">Beranda</a>
                    <a href="{{ route('user.catalog.index') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-stone-700 hover:bg-botanical-50">Katalog</a>
                    <a href="{{ route('user.favorites.index') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-stone-700 hover:bg-botanical-50">Favorit</a>
                    <a href="{{ route('user.cart.index') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-stone-700 hover:bg-botanical-50">Keranjang</a>
                    <a href="{{ route('user.orders.index') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-stone-700 hover:bg-botanical-50">Pesanan</a>
                    <a href="{{ route('user.profile.show') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-stone-700 hover:bg-botanical-50">Profil</a>
                    <form method="POST" action="{{ route('logout') }}" class="px-4 pt-2">
                        @csrf
                        <button type="submit" class="min-h-11 text-sm font-semibold text-stone-600">Keluar</button>
                    </form>
                </div>
            </div>
        </header>

        <main id="main-content" class="min-h-[65vh]">
            <div class="mx-auto max-w-7xl px-4 pt-5 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div role="status" class="flex items-start gap-3 rounded-2xl border border-botanical-200 bg-botanical-50 px-4 py-3 text-sm text-botanical-800">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div role="alert" class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div role="alert" class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
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

        <footer class="border-t border-line bg-cream/60">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-8 text-sm text-stone-600 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p class="font-semibold text-botanical-900">E-Commerce Bunga</p>
                <p>Bunga segar untuk setiap momen istimewa.</p>
            </div>
        </footer>
    </body>
</html>
