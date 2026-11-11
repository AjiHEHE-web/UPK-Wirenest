@if (auth()->check() && auth()->user()->role === 'user')
    <header class="sticky top-0 z-30 border-b border-line bg-white/95 backdrop-blur">
        <nav class="mx-auto flex min-h-20 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8" aria-label="Navigasi utama">
            <a href="{{ route('user.home') }}" class="flex shrink-0 items-center gap-3" aria-label="Bunga, kembali ke beranda">
                <x-application-logo class="h-11 w-11 fill-current text-botanical-700" />
                <span>
                    <span class="block font-semibold tracking-tight text-botanical-900">Bunga</span>
                    <span class="block text-[11px] font-medium tracking-[0.18em] text-stone-500">FLOWER SHOP</span>
                </span>
            </a>

            <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-lg border border-line text-ink transition hover:bg-cream lg:hidden" aria-label="Buka menu" aria-expanded="false" x-on:click="menuOpen = !menuOpen" x-bind:aria-expanded="menuOpen.toString()" x-bind:aria-label="menuOpen ? 'Tutup menu' : 'Buka menu'">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div class="hidden items-center gap-7 lg:flex">
                <a href="{{ route('user.home') }}" @class(['text-sm font-semibold text-botanical-900' => request()->routeIs('user.home'), 'text-sm font-medium text-stone-600 transition hover:text-botanical-800' => ! request()->routeIs('user.home')])>Beranda</a>
                <a href="{{ route('user.catalog.index') }}" class="text-sm font-medium text-stone-600 transition hover:text-botanical-800">Katalog</a>
                <a href="{{ route('user.favorites.index') }}" class="text-sm font-medium text-stone-600 transition hover:text-botanical-800">Favorit</a>
                <a href="{{ route('user.orders.index') }}" class="text-sm font-medium text-stone-600 transition hover:text-botanical-800">Pesanan</a>
            </div>

            <div class="hidden items-center gap-3 lg:flex">
                <a href="{{ route('user.cart.index') }}" class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-line px-4 text-sm font-semibold text-ink transition hover:border-botanical-300 hover:bg-botanical-50">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 4h2l2 12h11l2-8H6"/><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/></svg>
                    Keranjang
                </a>
                <a href="{{ route('user.profile.show') }}" class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full bg-botanical-100 text-sm font-semibold text-botanical-800 ring-1 ring-botanical-200 transition hover:ring-botanical-400" aria-label="Profil {{ auth()->user()->nama }}">
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
                <a href="{{ route('user.home') }}" class="rounded-lg px-4 py-3 text-sm font-semibold text-botanical-900 hover:bg-botanical-50">Beranda</a>
                <a href="{{ route('user.catalog.index') }}" class="rounded-lg px-4 py-3 text-sm font-medium text-stone-700 hover:bg-cream">Katalog</a>
                <a href="{{ route('user.favorites.index') }}" class="rounded-lg px-4 py-3 text-sm font-medium text-stone-700 hover:bg-cream">Favorit</a>
                <a href="{{ route('user.orders.index') }}" class="rounded-lg px-4 py-3 text-sm font-medium text-stone-700 hover:bg-cream">Pesanan</a>
                <a href="{{ route('user.cart.index') }}" class="rounded-lg px-4 py-3 text-sm font-medium text-stone-700 hover:bg-cream">Keranjang</a>
                <a href="{{ route('user.profile.show') }}" class="rounded-lg px-4 py-3 text-sm font-medium text-stone-700 hover:bg-cream">Profil</a>
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
                <x-application-logo class="h-9 w-9 fill-current text-botanical-100" />
                <span>WireNest <span class="font-normal text-white/65">Admin</span></span>
            </a>
            <div class="flex items-center gap-2 sm:gap-5">
                <a href="{{ route('admin.dashboard') }}" class="hidden min-h-11 items-center text-sm font-medium text-white/80 transition hover:text-white sm:flex">Dashboard</a>
                <a href="{{ route('admin.ar-models.index') }}" class="inline-flex min-h-11 items-center rounded-lg bg-white/10 px-3 text-sm font-semibold text-white transition hover:bg-white/15">Manajemen AR</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="min-h-11 rounded-full px-3 text-sm font-medium text-white/80 transition hover:bg-white/10 hover:text-white">Keluar</button>
                </form>
            </div>
        </nav>
    </header>
@else
    <header class="sticky top-0 z-30 border-b border-line bg-white/95 backdrop-blur">
        <div class="mx-auto flex min-h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('landing') }}" aria-label="WireNest, kembali ke beranda" class="flex min-h-11 shrink-0 items-center gap-3 rounded-lg">
                <x-application-logo class="h-9 w-9 fill-current text-botanical-700" />
                <span class="text-lg font-bold tracking-tight text-botanical-900">WireNest</span>
            </a>

            <nav aria-label="Navigasi utama" class="hidden items-center gap-7 md:flex">
                <a href="{{ route('landing') }}#pengalaman-ar" class="min-h-11 content-center text-sm font-medium text-stone-600 transition hover:text-botanical-800">Pengalaman AR</a>
                <a href="{{ route('landing') }}#cara-kerja" class="min-h-11 content-center text-sm font-medium text-stone-600 transition hover:text-botanical-800">Cara kerja</a>
            </nav>

            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl px-3 text-sm font-semibold text-botanical-800 transition hover:bg-botanical-50 sm:px-4">Masuk</a>
                <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-botanical-800 px-4 text-sm font-semibold text-white transition hover:bg-botanical-900 sm:px-5">Daftar</a>
            </div>
        </div>
    </header>
@endif
