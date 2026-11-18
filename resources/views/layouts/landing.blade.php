<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'WireNest | Bunga yang lebih dekat')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="storefront min-h-screen bg-cream text-ink">
        <a href="#main-content" class="sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:not-sr-only focus:rounded-lg focus:bg-white focus:px-4 focus:py-3 focus:text-ink">Lewati ke konten utama</a>

        @include('layouts.partials.storefront-navigation')

        <main id="main-content">
            @yield('content')
        </main>

        <footer class="border-t border-line bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-6 text-sm text-stone-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/wirenest-logo-themed.png') }}" alt="" class="h-9 w-9 shrink-0 rounded-full object-cover">
                    <span>WireNest · Bunga untuk momen yang berarti.</span>
                </div>
                <a href="{{ route('login') }}" class="font-semibold text-botanical-800 transition hover:text-botanical-950">Masuk ke akun</a>
            </div>
        </footer>
    </body>
</html>
