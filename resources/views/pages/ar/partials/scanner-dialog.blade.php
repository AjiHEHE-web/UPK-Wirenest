<dialog id="ar-scanner-dialog" data-ar-route-prefix="{{ rtrim(config('app.url'), '/').'/ar' }}" aria-labelledby="ar-scanner-title" class="w-[calc(100%-2rem)] max-w-xl rounded-2xl border border-line bg-white p-0 text-ink shadow-2xl backdrop:bg-ink/60">
    <div class="flex items-center justify-between gap-4 border-b border-line px-5 py-4">
        <div>
            <h2 id="ar-scanner-title" class="font-semibold text-ink">Arahkan kamera ke QR</h2>
            <p class="mt-1 text-xs text-stone-500">QR yang dipindai akan membuka halaman AR WireNest.</p>
        </div>
        <button type="button" data-ar-close-scanner class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg text-stone-500 transition hover:bg-stone-100 hover:text-ink focus-visible:outline focus-visible:outline-2 focus-visible:outline-botanical-700" aria-label="Tutup pemindai">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
        </button>
    </div>
    <div class="p-5">
        <div id="qr-reader" class="min-h-64 overflow-hidden rounded-xl bg-stone-950" aria-label="Pratinjau kamera untuk scan QR"></div>
        <p data-ar-dialog-status role="status" aria-live="polite" class="mt-4 min-h-6 text-sm text-stone-600">Meminta akses kamera...</p>
    </div>
</dialog>
