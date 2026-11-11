@php
    $class = $class ?? 'h-5 w-5';
@endphp

<svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('flower')
            <path d="M12 12c-2-2-2-5 0-7 2 2 2 5 0 7Z"/><path d="M12 12c2-2 5-2 7 0-2 2-5 2-7 0Z"/><path d="M12 12c2 2 2 5 0 7-2-2-2-5 0-7Z"/><path d="M12 12c-2 2-5 2-7 0 2-2 5-2 7 0Z"/><circle cx="12" cy="12" r="1.4"/>
            @break
        @case('dashboard')
            <rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="4" rx="1.5"/><rect x="13.5" y="10.5" width="7" height="10" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/>
            @break
        @case('package')
            <path d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z"/><path d="m3.8 7.6 8.2 4.5 8.2-4.5M12 12v9"/><path d="m7.5 5.4 8.5 4.7"/>
            @break
        @case('cube')
            <path d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z"/><path d="m3.8 7.6 8.2 4.5 8.2-4.5M12 12v9"/>
            @break
        @case('orders')
            <path d="M5 8h14l1 12H4L5 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>
            @break
        @case('credit-card')
            <rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3"/>
            @break
        @case('history')
            <path d="M3 12a9 9 0 1 0 2.6-6.4L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l3 2"/>
            @break
        @case('logout')
            <path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/>
            @break
        @case('panel')
            <rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16"/>
            @break
        @case('close')
            <path d="m18 6-12 12M6 6l12 12"/>
            @break
        @case('menu')
            <path d="M4 6h16M4 12h16M4 18h16"/>
            @break
        @case('search')
            <circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/>
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14"/>
            @break
        @case('eye')
            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/>
            @break
        @case('edit')
            <path d="m15 5 4 4M4 20l4-.8L19 8a2.1 2.1 0 0 0-3-3L5 16l-1 4Z"/>
            @break
        @case('trash')
            <path d="M4 7h16M10 11v6M14 11v6M5 7l1 14h12l1-14M9 7V4h6v3"/>
            @break
        @case('check')
            <path d="m5 12 4 4L19 6"/>
            @break
        @case('arrow')
            <path d="M5 12h14M13 6l6 6-6 6"/>
            @break
        @case('back')
            <path d="m15 18-6-6 6-6"/><path d="M9 12h12"/>
            @break
        @case('calendar')
            <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>
            @break
        @case('banknote')
            <rect x="2.5" y="5" width="19" height="14" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M6 9h.01M18 15h.01"/>
            @break
    @endswitch
</svg>
