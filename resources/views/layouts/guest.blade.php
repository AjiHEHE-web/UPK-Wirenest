<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $wide ? 'WireNest | Daftar' : 'WireNest | Masuk' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-ink antialiased">
        <main class="flex min-h-screen items-center justify-center bg-[#f3f2eb] px-4 py-8 sm:px-6 lg:py-12">
            <div class="w-full max-w-6xl">
                <header class="mb-5 flex items-center justify-between px-1">
                    <a href="/" aria-label="WireNest, beranda" class="flex min-h-11 items-center gap-3 rounded-lg">
                        <img src="{{ asset('images/wirenest-logo.png') }}" alt="WireNest" class="h-9 w-[3.375rem] shrink-0 object-contain">
                    </a>
                    <span class="hidden text-[11px] font-semibold uppercase tracking-[0.2em] text-botanical-800/60 sm:block">
                        Ruang akun
                    </span>
                </header>

                <div class="grid overflow-hidden rounded-[1.75rem] border border-line bg-white shadow-[0_26px_75px_-46px_rgba(34,49,42,0.38)] lg:grid-cols-[0.82fr_1.18fr]">
                    <aside aria-label="Tentang WireNest" class="relative hidden min-h-[580px] flex-col justify-between overflow-hidden bg-botanical-900 px-9 py-10 text-white lg:flex xl:px-12">
                        <div class="relative z-10 max-w-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-botanical-200">
                                WireNest <span class="px-1 text-botanical-400">/</span> Akun
                            </p>
                            <h2 class="mt-16 text-3xl font-semibold leading-tight tracking-tight xl:text-4xl">
                                {{ $wide ? 'Kirim bunga, sampaikan rasa yang tak terucap.' : 'Senang melihat Anda kembali.' }}
                            </h2>
                            <p class="mt-4 max-w-xs text-sm leading-6 text-white/70">
                                {{ $wide ? 'Buat akun untuk menjelajahi koleksi WireNest.' : 'Masuk untuk mengelola akun dan melanjutkan pesanan Anda.' }}
                            </p>
                        </div>

                        <svg viewBox="0 0 360 360" class="absolute -bottom-5 -right-5 w-[78%] max-w-[330px] text-white/90" fill="none" aria-hidden="true">
                            <path d="M220 360V220m0 117c-27-43-66-55-95-37 20 37 53 53 95 49m0-61c26-33 62-42 91-27-18 31-48 44-91 40m0-67c-19-23-43-26-61-11 17 25 37 32 61 21" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                            <path d="M220 220c-31-30-25-68 0-88 25 20 31 58 0 88Zm0 0c-39-7-59-37-48-68 34 6 56 33 48 68Zm0 0c-38 11-68-8-71-39 32-14 64 0 71 39Zm0 0c38 11 68-8 71-39-32-14-64 0-71 39Zm0 0c7-39 34-61 64-55 7 33-15 59-64 55Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" />
                            <circle cx="220" cy="220" r="6" fill="#d3a56e" />
                        </svg>
                    </aside>

                    <section class="min-w-0 px-6 py-9 sm:px-10 sm:py-12 lg:px-14 lg:py-14 {{ $wide ? '' : 'lg:flex lg:items-center' }}">
                        {{ $slot }}
                    </section>
                </div>
            </div>
        </main>
    </body>
</html>
