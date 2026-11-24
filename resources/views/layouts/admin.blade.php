@if (($page ?? null) === 'receipt')
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title') · E-Commerce Bunga</title>
        @vite(['resources/css/app.css'])
        <style>
            @media print {
                @page { margin: 16mm; }
                body { background: white !important; }
                .no-print { display: none !important; }
                .receipt { max-width: none !important; border: 0 !important; box-shadow: none !important; }
            }
        </style>
    </head>
    <body class="min-h-screen bg-cream px-4 py-8 text-ink sm:py-12">
        @yield('content')
    </body>
</html>
@else
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Dashboard') · E-Commerce Bunga</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js" defer></script>
    </head>
    <body x-data="{ sidebarOpen: false, sidebarCollapsed: false }" x-on:keydown.escape.window="sidebarOpen = false">
        <a href="#main-content" class="sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:not-sr-only focus:rounded-lg focus:bg-white focus:px-4 focus:py-3 focus:text-ink">Lewati ke konten utama</a>
        <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-30 bg-ink/45 backdrop-blur-sm lg:hidden" x-on:click="sidebarOpen = false"></div>

        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col bg-botanical-900 text-white transition-transform duration-200 lg:translate-x-0"
            :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0', sidebarCollapsed ? 'lg:w-24' : 'lg:w-72']"
            aria-label="Navigasi Admin"
        >
            <div class="flex h-20 items-center gap-3 border-b border-white/10 px-6">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-botanical-100">
                    <iconify-icon icon="lucide:flower" class="inline-block h-6 w-6" aria-hidden="true"></iconify-icon>
                </span>
                <div x-show="!sidebarCollapsed" x-transition.opacity class="min-w-0">
                    <p class="font-semibold tracking-wide">E-Commerce Bunga</p>
                    <p class="text-xs text-white/55">ADMIN PANEL</p>
                </div>
                <button type="button" class="ml-auto rounded-lg p-2 text-white/70 hover:bg-white/10 lg:hidden" aria-label="Tutup navigasi" x-on:click="sidebarOpen = false">
                    <iconify-icon icon="lucide:x" class="inline-block h-5 w-5" aria-hidden="true"></iconify-icon>
                </button>
                <button type="button" class="ml-auto hidden rounded-lg p-2 text-white/70 transition hover:bg-white/10 hover:text-white lg:block" aria-label="Ciutkan navigasi" x-on:click="sidebarCollapsed = !sidebarCollapsed">
                    <iconify-icon icon="lucide:panel-left" class="inline-block h-5 w-5" aria-hidden="true"></iconify-icon>
                </button>
            </div>

            <nav class="flex-1 space-y-7 overflow-y-auto px-4 py-6">
                @php($isPendingTransactionDetails = request()->routeIs('admin.transactions.show') && request()->route('payment')?->status_pembayaran === 'pending')
                <div>
                    <p x-show="!sidebarCollapsed" class="mb-3 px-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">Menu utama</p>
                    <a href="{{ route('admin.dashboard') }}" aria-label="Dashboard" @class([
                        'flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition duration-200',
                        'bg-white/15 font-semibold text-white' => request()->routeIs('admin.dashboard'),
                        'text-white/70 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.dashboard'),
                    ]) :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
                        <iconify-icon icon="lucide:layout-grid" class="block h-7 w-7 shrink-0 text-[26px]" aria-hidden="true"></iconify-icon>
                        <span x-show="!sidebarCollapsed" class="leading-7">Dashboard</span>
                    </a>
                </div>

                <div>
                    <p x-show="!sidebarCollapsed" class="mb-3 px-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">Katalog</p>
                    <div>
                        <a href="{{ route('admin.products.index') }}" aria-label="Manajemen Produk" @class([
                            'flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition duration-200',
                            'bg-white/15 font-semibold text-white' => request()->routeIs('admin.products.*'),
                            'text-white/70 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.products.*'),
                        ]) :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''" @if(request()->routeIs('admin.products.*')) aria-current="page" @endif>
                            <iconify-icon icon="lucide:box" class="block h-7 w-7 shrink-0 text-[26px]" aria-hidden="true"></iconify-icon>
                            <span x-show="!sidebarCollapsed" class="leading-7">Manajemen Produk</span>
                        </a>
                        <a href="{{ route('admin.ar-models.index') }}" aria-label="Manajemen AR" @class([
                            'flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition duration-200',
                            'bg-white/15 font-semibold text-white' => request()->routeIs('admin.ar-models.*'),
                            'text-white/70 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.ar-models.*'),
                        ]) :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''" @if(request()->routeIs('admin.ar-models.*')) aria-current="page" @endif>
                            <iconify-icon icon="lucide:box" class="block h-7 w-7 shrink-0 text-[26px]" aria-hidden="true"></iconify-icon>
                            <span x-show="!sidebarCollapsed" class="leading-7">Manajemen AR</span>
                        </a>
                    </div>
                </div>

                <div>
                    <p x-show="!sidebarCollapsed" class="mb-3 px-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">Operasional</p>
                    <div class="space-y-1">
                        <a href="{{ route('admin.orders.index') }}" aria-label="Pesanan" @class([
                            'flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition duration-200',
                            'bg-white/15 font-semibold text-white' => request()->routeIs('admin.orders.*'),
                            'text-white/70 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.orders.*'),
                        ]) :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''" @if(request()->routeIs('admin.orders.*')) aria-current="page" @endif>
                            <iconify-icon icon="lucide:shopping-bag" class="block h-7 w-7 shrink-0 text-[26px]" aria-hidden="true"></iconify-icon>
                            <span x-show="!sidebarCollapsed" class="leading-7">Pesanan</span>
                        </a>
                    </div>
                </div>

                <div>
                    <p x-show="!sidebarCollapsed" class="mb-3 px-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/45">Keuangan</p>
                    <div class="space-y-1">
                        <a href="{{ route('admin.transactions.pending') }}" aria-label="Konfirmasi Pembayaran" @class([
                            'flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition duration-200',
                            'bg-white/15 font-semibold text-white' => request()->routeIs('admin.transactions.pending') || $isPendingTransactionDetails,
                            'text-white/70 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.transactions.pending') && ! $isPendingTransactionDetails,
                        ]) :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''" @if(request()->routeIs('admin.transactions.pending')) aria-current="page" @endif>
                            <iconify-icon icon="lucide:credit-card" class="block h-7 w-7 shrink-0 text-[26px]" aria-hidden="true"></iconify-icon>
                            <span x-show="!sidebarCollapsed" class="leading-7">Konfirmasi Pembayaran</span>
                        </a>
                        <a href="{{ route('admin.refunds.index') }}" aria-label="Pengembalian Dana" @class([
                            'flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition duration-200',
                            'bg-white/15 font-semibold text-white' => request()->routeIs('admin.refunds.*'),
                            'text-white/70 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.refunds.*'),
                        ]) :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''" @if(request()->routeIs('admin.refunds.*')) aria-current="page" @endif>
                            <iconify-icon icon="lucide:banknote" class="block h-7 w-7 shrink-0 text-[26px]" aria-hidden="true"></iconify-icon>
                            <span x-show="!sidebarCollapsed" class="leading-7">Pengembalian Dana</span>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="border-t border-white/10 p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-white/70 transition hover:bg-white/10 hover:text-white" :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''">
                        <iconify-icon icon="lucide:log-out" class="block h-7 w-7 shrink-0 text-[26px]" aria-hidden="true"></iconify-icon>
                        <span x-show="!sidebarCollapsed" class="leading-7">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="min-h-screen transition-[padding] duration-200" :class="sidebarCollapsed ? 'lg:pl-24' : 'lg:pl-72'">
            <header class="sticky top-0 z-20 border-b border-line bg-cream/95 backdrop-blur">
                <div class="flex min-h-20 items-center justify-between gap-4 px-4 sm:px-7 lg:px-10">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-line bg-white text-ink transition hover:bg-botanical-50 lg:hidden" aria-label="Buka navigasi" x-on:click="sidebarOpen = true">
                            <iconify-icon icon="lucide:menu" class="inline-block h-5 w-5" aria-hidden="true"></iconify-icon>
                        </button>
                        <div class="min-w-0">
                            <div class="hidden items-center gap-2 text-xs text-stone-500 sm:flex">
                                <span>Admin</span>
                                <span aria-hidden="true">/</span>
                                <span class="truncate text-botanical-700">@yield('breadcrumb', 'Dashboard')</span>
                            </div>
                            <h1 class="truncate text-lg font-semibold text-ink sm:text-xl">@yield('title', 'Dashboard')</h1>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-3">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-semibold text-ink">{{ auth()->user()->nama ?: auth()->user()->name }}</p>
                            <p class="text-xs text-stone-500">Administrator</p>
                        </div>
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-botanical-100 text-sm font-semibold text-botanical-800" aria-label="Profil Administrator">
                            {{ mb_strtoupper(mb_substr(auth()->user()->nama ?: auth()->user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <main id="main-content" class="mx-auto max-w-[1600px] px-4 py-6 sm:px-7 sm:py-8 lg:px-10">
                @include('pages.admin.partials.flash')
                @yield('content')
            </main>
        </div>
    </body>
</html>
@endif
