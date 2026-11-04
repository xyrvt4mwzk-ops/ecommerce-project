@props([
    'title',
    'subtitle' => null,
])

<div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div class="max-w-3xl">
        <h1 class="text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap gap-3">
            {{ $actions }}
        </div>
    @endisset
</div>
