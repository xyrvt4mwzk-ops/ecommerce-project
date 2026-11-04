@props([
    'label',
    'value',
    'hint' => null,
    'accent' => 'indigo',
])

@php
    $tones = [
        'indigo' => 'from-indigo-600 to-slate-900 text-white',
        'violet' => 'from-violet-600 to-fuchsia-600 text-white',
        'slate' => 'from-slate-900 to-slate-700 text-white',
        'neutral' => 'from-white to-slate-100 text-slate-900',
    ];
    $tone = $tones[$accent] ?? $tones['neutral'];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white p-5 shadow-sm']) }}>
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $value }}</p>
            @if($hint)
                <p class="mt-2 text-sm text-slate-500">{{ $hint }}</p>
            @endif
        </div>
        <div class="rounded-lg bg-gradient-to-br px-3 py-2 text-sm font-semibold shadow-sm {{ $tone }}">
            {{ $slot }}
        </div>
    </div>
</div>
