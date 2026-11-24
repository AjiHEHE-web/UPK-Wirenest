@props(['status'])

@php
    $styles = [
        'pending' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'processing' => 'bg-indigo-50 text-indigo-800 ring-indigo-200',
        'shipped' => 'bg-violet-50 text-violet-800 ring-violet-200',
        'completed' => 'bg-botanical-50 text-botanical-800 ring-botanical-200',
        'paid' => 'bg-botanical-50 text-botanical-800 ring-botanical-200',
        'failed' => 'bg-rose-50 text-rose-800 ring-rose-200',
        'cancelled' => 'bg-stone-100 text-stone-700 ring-stone-200',
        'rejected' => 'bg-rose-50 text-rose-800 ring-rose-200',
    ];
    $labels = [
        'pending' => 'Pending',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'paid' => 'Dibayar',
        'failed' => 'Ditolak',
        'cancelled' => 'Dibatalkan',
        'rejected' => 'Ditolak',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold capitalize ring-1 ring-inset '.($styles[$status] ?? 'bg-stone-100 text-stone-700 ring-stone-200')]) }}>
    {{ $labels[$status] ?? str_replace('_', ' ', $status) }}
</span>
