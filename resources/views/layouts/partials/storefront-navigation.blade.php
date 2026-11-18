@if (auth()->check() && auth()->user()->role === 'user')
    @php($cartCount = array_sum(session('cart', [])))
    <header x-bind:class="scrolled ? 'max-w-5xl' : 'max-w-7xl'" class="sticky top-0 z-30 mx-auto w-full px-3 pt-3 transition-all duration-300 ease-out sm:px-5">
        <nav x-bind:class="scrolled ? 'min-h-[60px] px-3 sm:px-5' : 'min-h-[76px] px-4 sm:px-6 lg:px-8'" class="flex items-center justify-between gap-3 rounded-full border border-white/80 bg-matcha/85 shadow-[0_10px_30px_rgba(40,70,50,.12)] backdrop-blur-xl transition-all duration-300 ease-out" aria-label="Navigasi utama">
            <a href="{{ route('user.home') }}" class="flex shrink-0 items-center gap-3 transition-all duration-300" aria-label="WireNest, kembali ke beranda">
                <img src="{{ asset('images/wirenest-logo-themed.png') }}" alt="" x-bind:class="scrolled ? 'h-9 w-9' : 'h-11 w-11'" class="shrink-0 rounded-full object-cover transition-all duration-300">
                <span>
                    <span class="block font-display text-xl font-semibold leading-none text-botanical-900">WireNest</span>
                    <span class="mt-1 block text-[9px] font-medium tracking-[0.13em] text-stone-600">ARTISANAL CHENILLE STEMS</span>
                </span>
            </a>

            <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-line text-ink transition hover:bg-cream lg:hidden" aria-label="Buka menu" aria-expanded="false" x-on:click="menuOpen = !menuOpen" x-bind:aria-expanded="menuOpen.toString()" x-bind:aria-label="menuOpen ? 'Tutup menu' : 'Buka menu'">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div class="hidden items-center gap-2 lg:flex">
                <a href="{{ route('user.home') }}" @class(['rounded-full px-5 py-2 text-sm font-semibold transition', 'bg-[#fbfbf6] text-ink shadow-sm' => request()->routeIs('user.home'), 'text-stone-600 hover:bg-cream hover:text-botanical-900' => ! request()->routeIs('user.home')]) @if (request()->routeIs('user.home')) aria-current="page" @endif>Beranda</a>
                <a href="{{ route('user.catalog.index') }}" @class(['rounded-full px-5 py-2 text-sm font-semibold transition', 'bg-[#fbfbf6] text-ink shadow-sm' => request()->routeIs('user.catalog.*'), 'text-stone-600 hover:bg-cream hover:text-botanical-900' => ! request()->routeIs('user.catalog.*')]) @if (request()->routeIs('user.catalog.*')) aria-current="page" @endif>Katalog</a>
                <a href="{{ route('user.orders.index') }}" @class(['rounded-full px-5 py-2 text-sm font-semibold transition', 'bg-[#fbfbf6] text-ink shadow-sm' => request()->routeIs('user.orders.*'), 'text-stone-600 hover:bg-cream hover:text-botanical-900' => ! request()->routeIs('user.orders.*')]) @if (request()->routeIs('user.orders.*')) aria-current="page" @endif>Riwayat</a>
            </div>

            <div class="hidden items-center gap-4 lg:flex">
                <a href="{{ route('user.favorites.index') }}" data-favorites-link @class(['inline-flex h-11 w-11 items-center justify-center rounded-full transition', 'bg-[#fbfbf6] text-ink shadow-sm' => request()->routeIs('user.favorites.*'), 'text-stone-700 hover:bg-cream hover:text-botanical-900' => ! request()->routeIs('user.favorites.*')]) aria-label="Favorit" title="Favorit" @if (request()->routeIs('user.favorites.*')) aria-current="page" @endif>
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <a href="{{ route('user.cart.index') }}" data-cart-link class="relative inline-flex h-11 w-11 items-center justify-center rounded-full text-ink transition hover:bg-cream" aria-label="{{ $cartCount > 0 ? 'Keranjang belanja, '.$cartCount.' item' : 'Keranjang belanja' }}" @if (request()->routeIs('user.cart.*')) aria-current="page" @endif>
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 4h2l2.2 11.5a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 1.9-1.5L21 8H6"/><circle cx="9.5" cy="20" r="1"/><circle cx="17.5" cy="20" r="1"/></svg>
                    <span data-cart-count aria-hidden="true" @if ($cartCount === 0) hidden @endif class="{{ $cartCount === 0 ? 'hidden' : '' }} absolute -right-0.5 -top-0.5 grid min-h-[18px] min-w-[18px] place-items-center rounded-full bg-botanical-700 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white">{{ $cartCount }}</span>
                </a>
                <span data-cart-status class="sr-only" aria-live="polite" aria-atomic="true"></span>
                <a href="{{ route('user.profile.show') }}" class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-full border border-botanical-200 bg-botanical-50 text-botanical-800 shadow-sm transition hover:ring-2 hover:ring-botanical-200" aria-label="Profil {{ auth()->user()->nama }}" @if (request()->routeIs('user.profile.*')) aria-current="page" @endif>
                    @if (auth()->user()->profil_user)
                        <img src="{{ Storage::disk('public')->url(auth()->user()->profil_user) }}" alt="" class="h-full w-full object-cover">
                    @else
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Zm0 0c2-2 5-2 7 0-2 2-5 2-7 0Zm0 0c2 2 2 5 0 7-2-2-2-5 0-7Zm0 0c-2-2-5-2-7 0 2 2 5 2 7 0Z"/><circle cx="12" cy="12" r="1.4"/></svg>
                    @endif
                </a>
            </div>
        </nav>

        <div x-cloak x-show="menuOpen" x-transition class="mt-2 overflow-hidden rounded-[1.75rem] border border-white/80 bg-matcha/90 px-3 py-3 shadow-[0_10px_30px_rgba(40,70,50,.12)] backdrop-blur-xl lg:hidden">
            <div class="mx-auto flex max-w-7xl flex-col gap-1">
                <a href="{{ route('user.home') }}" class="rounded-xl px-4 py-3 text-sm font-semibold text-botanical-900 hover:bg-botanical-50">Beranda</a>
                <a href="{{ route('user.catalog.index') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-stone-700 hover:bg-cream">Katalog</a>
                <a href="{{ route('user.favorites.index') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-stone-700 hover:bg-cream">Favorit</a>
                <a href="{{ route('user.orders.index') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-stone-700 hover:bg-cream">Riwayat</a>
                <a href="{{ route('user.cart.index') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-stone-700 hover:bg-cream">Keranjang</a>
                <a href="{{ route('user.profile.show') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-stone-700 hover:bg-cream">Profil</a>
                <form method="POST" action="{{ route('logout') }}" class="px-4 pt-2">
                    @csrf
                    <button type="submit" class="min-h-11 text-sm font-semibold text-stone-600">Keluar</button>
                </form>
            </div>
        </div>
    </header>
@elseif (auth()->check() && auth()->user()->role === 'admin')
    <header class="sticky top-0 z-30 border-b border-botanical-950 bg-botanical-900 text-white">
        <nav class="mx-auto flex min-h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8" aria-label="Navigasi admin">
            <a href="{{ route('admin.dashboard') }}" class="flex min-h-11 items-center gap-3 font-semibold" aria-label="Dashboard admin">
                <img src="{{ asset('images/wirenest-logo-themed.png') }}" alt="" class="h-9 w-9 shrink-0 rounded-full object-cover">
                <span>WireNest <span class="font-normal text-white/65">Admin</span></span>
            </a>
            <div class="flex items-center gap-2 sm:gap-5">
                <a href="{{ route('admin.dashboard') }}" class="hidden min-h-11 items-center text-sm font-medium text-white/80 transition hover:text-white sm:flex">Dashboard</a>
                <a href="{{ route('admin.ar-models.index') }}" class="inline-flex min-h-11 items-center rounded-lg bg-matcha/10 px-3 text-sm font-semibold text-white transition hover:bg-matcha/15">Manajemen AR</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="min-h-11 rounded-full px-3 text-sm font-medium text-white/80 transition hover:bg-matcha/10 hover:text-white">Keluar</button>
                </form>
            </div>
        </nav>
    </header>
@else
    <header class="sticky top-0 z-30 mx-auto w-full max-w-7xl px-3 pt-3 transition-all duration-300 sm:px-5">
        <nav class="flex min-h-[76px] items-center justify-between gap-3 rounded-full border border-white/80 bg-matcha/85 px-4 shadow-[0_10px_30px_rgba(40,70,50,.12)] backdrop-blur-xl transition-all duration-300 sm:px-6 lg:px-8" aria-label="Navigasi utama">
            <a href="{{ route('landing') }}" aria-label="WireNest, kembali ke beranda" class="flex min-h-11 shrink-0 items-center gap-3 rounded-lg">
                <img src="{{ asset('images/wirenest-logo-themed.png') }}" alt="" class="h-11 w-11 shrink-0 rounded-full object-cover">
                <span>
                    <span class="block font-display text-xl font-semibold leading-none text-botanical-900">WireNest</span>
                    <span class="mt-1 hidden text-[9px] font-medium tracking-[0.13em] text-stone-600 sm:block">ARTISANAL CHENILLE STEMS</span>
                </span>
            </a>

            <nav aria-label="Navigasi halaman" class="hidden items-center gap-2 lg:flex">
                <a href="{{ route('landing') }}" class="rounded-full bg-[#fbfbf6] px-5 py-2 text-sm font-semibold text-ink shadow-sm" aria-current="page">Beranda</a>
                <a href="{{ route('user.catalog.index') }}" class="rounded-full px-5 py-2 text-sm font-semibold text-stone-600 transition hover:bg-cream hover:text-botanical-900">Katalog</a>
            </nav>

            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center rounded-full px-4 text-sm font-semibold text-botanical-800 transition hover:bg-cream">Masuk</a>
                <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center justify-center rounded-full bg-botanical-800 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-botanical-900">Daftar</a>
            </div>
        </nav>
    </header>
@endif
