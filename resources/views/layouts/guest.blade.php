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
                        <img src="{{ asset('images/wirenest-logo-themed.png') }}" alt="" class="h-9 w-9 shrink-0 rounded-full object-cover">
                        <span class="text-lg font-bold tracking-tight text-botanical-900">WireNest</span>
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

                        <svg viewBox="0 0 360 360" class="absolute -bottom-5 -right-5 w-[78%] max-w-[330px] text-botanical-300/75" fill="none" aria-hidden="true">
                            <path d="M190 356c-10-63-8-122 5-184m-3 118c-48-43-92-43-128-25 31 43 72 57 128 46m-2-75c40-47 82-57 124-44-27 44-68 65-124 60" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            <path d="M192 181c-35-5-51-31-34-57 29 1 46 22 34 57Zm3-8c-14-33 1-61 30-65 18 27 8 55-30 65Zm-17 11c-8-34 11-59 39-58 13 30-3 55-39 58Zm25-2c12-33 39-43 62-30-7 32-32 46-62 30Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                            <circle cx="192" cy="183" r="7" fill="#d3a56e" />
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
