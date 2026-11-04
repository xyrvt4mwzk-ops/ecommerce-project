@props(['status'])

@php
    $map = [
        'draft' => 'border-slate-200 bg-slate-100 text-slate-700',
        'published' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'archived' => 'border-amber-200 bg-amber-50 text-amber-700',
        'pending' => 'border-amber-200 bg-amber-50 text-amber-700',
        'paid' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'failed' => 'border-rose-200 bg-rose-50 text-rose-700',
        'cancelled' => 'border-slate-200 bg-slate-50 text-slate-600',
        'refunded' => 'border-sky-200 bg-sky-50 text-sky-700',
    ];
    $classes = $map[$status] ?? 'bg-slate-100 text-slate-700';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-md border px-2.5 py-1 text-xs font-semibold '.$classes]) }}>
    {{ ucfirst($status) }}
</span>
