@extends('layouts.ar')

@section('title', $arModel->nama_model.' · WireNest AR')

@if ($modelFormat === 'glb')
    @push('scripts')
        <script src="https://aframe.io/releases/1.7.1/aframe.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@ar-js-org/ar.js@3.4.8/aframe/build/aframe-ar.js"></script>
    @endpush
@endif

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-8 sm:px-6 sm:py-12">
        <div class="rounded-3xl border border-line bg-white p-5 shadow-sm sm:p-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="store-eyebrow">Model augmented reality</p>
                    <h1 class="store-title mt-2 text-3xl sm:text-4xl">{{ $arModel->nama_model }}</h1>
                    <p class="mt-2 text-sm text-stone-500">Format {{ strtoupper($modelFormat) }} · Geser untuk memutar, cubit untuk memperbesar.</p>
                </div>
                <a href="{{ $modelUrl }}" download class="inline-flex min-h-11 items-center justify-center rounded-xl border border-line px-4 text-sm font-semibold text-botanical-800 transition hover:bg-botanical-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-700">Unduh model 3D</a>
            </div>

            <div data-ar-viewer data-model-url="{{ $modelUrl }}" data-model-format="{{ $modelFormat }}" class="mt-6">
                <div data-ar-stage class="relative h-[min(64vh,560px)] min-h-[320px] overflow-hidden rounded-2xl bg-[#f3f1e8]">
                    <p data-ar-loading role="status" aria-live="polite" class="absolute inset-0 z-10 flex items-center justify-center px-6 text-center text-sm text-stone-600">Menyiapkan model 3D...</p>
                    <p data-ar-error role="alert" hidden class="absolute inset-x-4 top-4 z-20 rounded-xl border border-rose-200 bg-white/95 p-4 text-sm text-rose-800 shadow-sm"></p>
                </div>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p data-ar-capability role="status" aria-live="polite" class="text-sm leading-5 text-stone-600">Model 3D dapat diputar langsung di halaman ini.</p>
                    <div class="flex flex-wrap gap-2">
                        @if ($modelFormat === 'glb')
                            <button type="button" data-ar-marker-start hidden class="inline-flex min-h-11 items-center justify-center rounded-xl bg-botanical-700 px-5 text-sm font-semibold text-white transition hover:bg-botanical-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-700">Lihat pada marker</button>
                        @else
                            <span class="inline-flex min-h-11 items-center rounded-xl bg-stone-100 px-4 text-sm text-stone-600">AR marker memerlukan berkas GLB</span>
                        @endif
                        <button type="button" data-ar-enter disabled class="inline-flex min-h-11 items-center justify-center rounded-xl border border-line px-4 text-sm font-semibold text-botanical-800 transition hover:bg-botanical-50 disabled:cursor-not-allowed disabled:text-stone-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-700">AR bebas</button>
                    </div>
                </div>
            </div>

            @if ($modelFormat === 'glb')
                <section data-ar-marker-experience data-model-url="{{ $modelUrl }}" class="mt-6 rounded-2xl border border-line bg-stone-50 p-4 sm:p-5" aria-labelledby="ar-marker-title">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 id="ar-marker-title" class="font-semibold text-ink">Tampilkan model di atas marker</h2>
                            <p class="mt-1 max-w-2xl text-sm leading-6 text-stone-600">Cetak marker Hiro, lalu arahkan kamera ke marker tersebut. Model akan muncul dan mengikuti marker.</p>
                            <a href="https://ar-js-org.github.io/studio/assets/default-marker.png" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex min-h-10 items-center text-sm font-semibold text-botanical-800 underline decoration-botanical-300 underline-offset-4 hover:text-botanical-950">Buka marker Hiro untuk dicetak</a>
                        </div>
                        <button type="button" data-ar-marker-stop hidden class="inline-flex min-h-11 items-center justify-center rounded-xl border border-line bg-white px-4 text-sm font-semibold text-stone-700 transition hover:bg-stone-100">Matikan kamera</button>
                    </div>
                    <p data-ar-marker-status role="status" aria-live="polite" class="mt-3 text-sm text-stone-600">Fitur AR marker akan tersedia setelah marker disiapkan.</p>
                    <div data-ar-marker-stage hidden class="mt-4 h-[min(65vh,560px)] min-h-[320px] overflow-hidden rounded-xl bg-stone-950"></div>
                </section>
            @endif
        </div>
    </section>
@endsection
