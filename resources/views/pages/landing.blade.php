@extends('layouts.landing')

@section('title', 'WireNest | Bunga yang bisa kamu lihat dalam AR')

@section('content')
    <section class="relative isolate overflow-hidden border-b border-line bg-[#f3f1e8]">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-[1.05fr_0.95fr] lg:px-8 lg:py-24">
            <div class="relative z-10">
                <p class="store-eyebrow inline-flex items-center gap-3">
                    <span class="h-px w-8 bg-botanical-500" aria-hidden="true"></span>
                    Bunga, kini lebih dekat
                </p>
                <h1 class="store-title mt-5 max-w-2xl text-4xl sm:text-5xl lg:text-6xl">
                    Rangkaian bunga yang <span class="italic text-botanical-700">bisa kamu lihat dalam AR.</span>
                </h1>
                <p class="mt-6 max-w-xl text-base leading-7 text-stone-600 sm:text-lg">
                    Temukan rangkaian untuk momen istimewa, lalu hidupkan model bunganya dalam augmented reality. Scan QR langsung dari sini, tanpa perlu masuk akun.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <button type="button" data-ar-open-scanner class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-botanical-800 px-6 text-sm font-semibold text-white transition duration-200 hover:bg-botanical-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-700">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V5a1 1 0 0 1 1-1h3m8 0h3a1 1 0 0 1 1 1v3m0 8v3a1 1 0 0 1-1 1h-3M8 20H5a1 1 0 0 1-1-1v-3"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 8h3v3H8zm5 5h3v3h-3zm1-5h2v2h-2zm-6 6h2v2H8z"/>
                        </svg>
                        Scan QR AR
                    </button>
                    <a href="{{ route('user.catalog.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-botanical-200 bg-white/80 px-6 text-sm font-semibold text-botanical-800 transition duration-200 hover:border-botanical-400 hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-700">
                        Jelajahi koleksi
                    </a>
                </div>
                <p class="mt-4 text-sm text-stone-500">Kamera akan aktif setelah kamu memberi izin.</p>
            </div>

            <div class="relative mx-auto w-full max-w-xl">
                <div class="absolute -left-8 top-8 h-28 w-28 rounded-full bg-botanical-200/70 blur-2xl sm:h-40 sm:w-40" aria-hidden="true"></div>
                <div class="absolute -bottom-5 -right-4 h-36 w-36 rounded-full bg-[#ead6c1]/80 blur-2xl sm:h-48 sm:w-48" aria-hidden="true"></div>
                <div class="relative overflow-hidden rounded-[2rem] border-8 border-white bg-botanical-100 shadow-[0_24px_60px_-38px_rgba(34,49,42,0.55)] ring-1 ring-botanical-200">
                    <div class="aspect-[4/3]">
                        <svg viewBox="0 0 560 420" class="h-full w-full" role="img" aria-labelledby="landing-flower-title">
                            <title id="landing-flower-title">Ilustrasi rangkaian bunga WireNest</title>
                            <rect width="560" height="420" fill="#e1ece1"/>
                            <circle cx="430" cy="90" r="52" fill="#f3f1e8"/>
                            <path d="M278 370c-8-73-7-137 4-210m-2 150c-58-55-111-57-153-36 37 52 84 73 153 61m1-73c47-51 97-62 148-47-31 50-80 73-148 67" fill="none" stroke="#385f49" stroke-linecap="round" stroke-linejoin="round" stroke-width="9"/>
                            <path d="M280 179c-40-7-58-37-40-67 34 1 53 26 40 67Zm8-11c-16-38 2-70 35-75 21 31 9 63-35 75Zm-20 15c-10-39 13-68 45-66 15 34-4 63-45 66Zm28-3c14-38 45-50 71-35-8 37-37 53-71 35Z" fill="#f3d9d5" stroke="#8f625f" stroke-linejoin="round" stroke-width="6"/>
                            <circle cx="278" cy="180" r="12" fill="#d3a56e"/>
                            <path d="m210 246 68 25 68-25-31 114h-74z" fill="#c9956d"/>
                            <path d="m225 254 53 20 53-20" fill="none" stroke="#fff9ef" stroke-linecap="round" stroke-width="7"/>
                            <path d="M211 247c-31-13-51-36-62-67 33 8 55 27 62 67Zm133 0c28-15 47-38 55-69-32 10-52 31-55 69Z" fill="#6f9c7d"/>
                            <path d="M165 180c-22-5-31-24-20-42 19 1 28 17 20 42Zm112-82c-30-10-38-37-19-58 24 5 35 27 19 58Zm116 44c-9-29 5-51 30-54 13 24 2 47-30 54Z" fill="#fff9ef" stroke="#6f9c7d" stroke-width="4"/>
                            <circle cx="165" cy="179" r="8" fill="#d3a56e"/>
                            <circle cx="277" cy="99" r="8" fill="#d3a56e"/>
                            <circle cx="393" cy="143" r="8" fill="#d3a56e"/>
                        </svg>
                    </div>
                    <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between gap-3 rounded-2xl border border-white/80 bg-white/95 p-4 shadow-sm sm:bottom-6 sm:left-6 sm:right-6">
                        <div>
                            <p class="store-eyebrow">Pengalaman WireNest</p>
                            <p class="mt-1 font-display text-lg text-botanical-950">Lihat sebelum memilih</p>
                        </div>
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-botanical-50 text-botanical-800" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3v18m9-9H3m15.36-6.36-12.72 12.72m12.72 0L5.64 5.64"/></svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="pengalaman-ar" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-18 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <p class="store-eyebrow">Lebih dari sekadar rangkaian</p>
            <h2 class="store-title mt-3 text-3xl sm:text-4xl">Bunga yang terasa semakin dekat</h2>
            <p class="mt-4 text-base leading-7 text-stone-600">Pilih hadiah dengan lebih yakin dan nikmati pengalaman baru, dari layar ke ruang di sekitarmu.</p>
        </div>

        <div class="mt-9 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <article class="rounded-2xl border border-line bg-white p-6 shadow-sm">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-botanical-50 text-botanical-800" aria-hidden="true">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Zm0 0c2-2 5-2 7 0-2 2-5 2-7 0Zm0 0c2 2 2 5 0 7-2-2-2-5 0-7Zm0 0c-2-2-5-2-7 0 2 2 5 2 7 0Z"/><circle cx="12" cy="12" r="1.4"/></svg>
                </span>
                <h3 class="mt-5 font-display text-xl font-medium text-botanical-950">Pilihan penuh makna</h3>
                <p class="mt-2 text-sm leading-6 text-stone-600">Temukan rangkaian bunga untuk merayakan momen kecil maupun hari paling istimewa.</p>
            </article>
            <article class="rounded-2xl border border-line bg-white p-6 shadow-sm">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-botanical-50 text-botanical-800" aria-hidden="true">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V5a1 1 0 0 1 1-1h3m8 0h3a1 1 0 0 1 1 1v3m0 8v3a1 1 0 0 1-1 1h-3M8 20H5a1 1 0 0 1-1-1v-3"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 8h3v3H8zm5 5h3v3h-3z"/></svg>
                </span>
                <h3 class="mt-5 font-display text-xl font-medium text-botanical-950">Lihat dengan AR</h3>
                <p class="mt-2 text-sm leading-6 text-stone-600">Arahkan kamera ke QR dan lihat model bunga 3D langsung dari perangkatmu.</p>
            </article>
            <article class="rounded-2xl border border-line bg-white p-6 shadow-sm sm:col-span-2 lg:col-span-1">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-botanical-50 text-botanical-800" aria-hidden="true">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2.5M21 12a9 9 0 1 1-2.64-6.36M21 4v5h-5"/></svg>
                </span>
                <h3 class="mt-5 font-display text-xl font-medium text-botanical-950">Mulai tanpa akun</h3>
                <p class="mt-2 text-sm leading-6 text-stone-600">Coba pemindai QR sebagai tamu. Login hanya diperlukan saat ingin berbelanja.</p>
            </article>
        </div>
    </section>

    <section id="cara-kerja" class="bg-botanical-950 text-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 sm:py-18 lg:grid-cols-[0.8fr_1.2fr] lg:px-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-botanical-200">Mulai dalam tiga langkah</p>
                <h2 class="mt-3 font-display text-3xl font-medium leading-tight sm:text-4xl">Bawa bunga ke ruangmu.</h2>
                <p class="mt-4 max-w-md text-sm leading-6 text-white/70">Tidak perlu membuat akun untuk mencoba pengalaman AR. Cukup siapkan QR dari kemasan WireNest.</p>
                <a href="{{ route('register') }}" class="mt-7 inline-flex min-h-12 items-center justify-center rounded-xl bg-white px-6 text-sm font-semibold text-botanical-900 transition hover:bg-botanical-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Buat akun WireNest</a>
            </div>
            <ol class="grid gap-3 sm:grid-cols-3">
                <li class="rounded-2xl border border-white/15 bg-white/5 p-5">
                    <span class="text-sm font-semibold text-botanical-300">01</span>
                    <h3 class="mt-4 font-semibold">Siapkan QR</h3>
                    <p class="mt-2 text-sm leading-6 text-white/65">Temukan kode QR AR pada kemasan produk WireNest.</p>
                </li>
                <li class="rounded-2xl border border-white/15 bg-white/5 p-5">
                    <span class="text-sm font-semibold text-botanical-300">02</span>
                    <h3 class="mt-4 font-semibold">Buka pemindai</h3>
                    <p class="mt-2 text-sm leading-6 text-white/65">Tekan Scan QR AR dan izinkan akses kamera perangkat.</p>
                </li>
                <li class="rounded-2xl border border-white/15 bg-white/5 p-5">
                    <span class="text-sm font-semibold text-botanical-300">03</span>
                    <h3 class="mt-4 font-semibold">Lihat model 3D</h3>
                    <p class="mt-2 text-sm leading-6 text-white/65">Arahkan kamera ke kode untuk membuka pengalaman AR.</p>
                </li>
            </ol>
        </div>
    </section>

    @include('pages.ar.partials.scanner-dialog')
@endsection
