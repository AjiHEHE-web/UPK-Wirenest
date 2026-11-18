@props(['paginator'])

<nav class="relative flex items-center justify-center gap-2 sm:gap-3" aria-label="Navigasi halaman">
    @if ($paginator->previousPageUrl())
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex min-h-11 items-center gap-2 rounded-full border border-botanical-200 bg-white px-5 text-sm font-semibold text-botanical-800 shadow-sm transition-colors hover:bg-[#e8f1e9] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-700">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            Sebelumnya
        </a>
    @else
        <span aria-disabled="true" class="inline-flex min-h-11 cursor-not-allowed items-center gap-2 rounded-full border border-botanical-100 bg-[#f1f5f0] px-5 text-sm font-semibold text-stone-400">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            Sebelumnya
        </span>
    @endif

    <span class="absolute left-0 top-1/2 hidden -translate-y-1/2 text-sm text-stone-600 sm:block" aria-live="polite">Halaman <span class="font-semibold text-botanical-800">{{ $paginator->currentPage() }}</span> dari {{ $paginator->lastPage() }}</span>

    @if ($paginator->nextPageUrl())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-botanical-800 px-5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-botanical-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-700">
            Selanjutnya
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
        </a>
    @else
        <span aria-disabled="true" class="inline-flex min-h-11 cursor-not-allowed items-center gap-2 rounded-full bg-[#dce8de] px-5 text-sm font-semibold text-botanical-500">
            Selanjutnya
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
        </span>
    @endif
</nav>
