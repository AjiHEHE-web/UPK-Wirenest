<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'WireNest AR')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body x-data="{ menuOpen: false }" x-on:keydown.escape.window="menuOpen = false" class="storefront min-h-screen bg-cream text-ink">
        <a href="#main-content" class="sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:not-sr-only focus:rounded-lg focus:bg-white focus:px-4 focus:py-3 focus:text-ink">Lewati ke konten utama</a>
        @include('layouts.partials.storefront-navigation')
        <main id="main-content" class="min-h-[calc(100vh-9rem)]">
            @yield('content')
        </main>
        <footer class="border-t border-line bg-white">
            <div class="mx-auto max-w-6xl px-4 py-5 text-xs text-stone-500 sm:px-6 lg:px-8">WireNest · Pengalaman AR untuk setiap momen.</div>
        </footer>
        @stack('scripts')
    </body>
</html>
