@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-stone-500">
            Menampilkan
            @if ($paginator->firstItem())
                <span class="font-semibold text-botanical-800">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</span>
            @else
                <span class="font-semibold text-botanical-800">0</span>
            @endif
            dari <span class="font-semibold text-botanical-800">{{ $paginator->total() }}</span> data
        </p>

        <div class="flex items-center justify-between gap-2 sm:justify-end">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="inline-flex h-10 w-10 cursor-not-allowed items-center justify-center rounded-l-lg border border-botanical-200 bg-botanical-50 text-botanical-300" aria-hidden="true">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-l-lg border border-botanical-200 bg-white text-botanical-800 transition hover:bg-botanical-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-500">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                </a>
            @endif

            <div class="-ml-px flex items-center">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-disabled="true" class="-ml-px inline-flex h-10 items-center border border-botanical-200 bg-white px-3 text-sm text-stone-500">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="-ml-px inline-flex h-10 min-w-10 items-center justify-center border border-botanical-800 bg-botanical-800 px-3 text-sm font-semibold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}" class="-ml-px inline-flex h-10 min-w-10 items-center justify-center border border-botanical-200 bg-white px-3 text-sm font-medium text-botanical-800 transition hover:bg-botanical-50 focus-visible:z-10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-500">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" class="-ml-px inline-flex h-10 w-10 items-center justify-center rounded-r-lg border border-botanical-200 bg-white text-botanical-800 transition hover:bg-botanical-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-500">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="-ml-px inline-flex h-10 w-10 cursor-not-allowed items-center justify-center rounded-r-lg border border-botanical-200 bg-botanical-50 text-botanical-300" aria-hidden="true">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 01-1.414 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
