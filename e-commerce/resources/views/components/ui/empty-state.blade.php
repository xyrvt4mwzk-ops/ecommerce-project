@props([
    'title',
    'message',
    'actionLabel' => null,
    'actionHref' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm']) }}>
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-fuchsia-600 text-white">
        {{ $slot }}
    </div>
    <h3 class="mt-5 text-xl font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $message }}</p>
    @if($actionLabel && $actionHref)
        <a href="{{ $actionHref }}" class="mt-6 inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">
            {{ $actionLabel }}
        </a>
    @endif
</div>
