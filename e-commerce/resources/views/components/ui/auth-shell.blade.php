@props([
    'title',
    'subtitle' => null,
    'panelTitle' => 'DigitalMarket',
    'panelText' => 'A premium digital products marketplace with role-based onboarding and secure downloads.',
])

<div class="mx-auto grid max-w-5xl gap-0 overflow-hidden rounded-[2rem] border border-slate-200/80 bg-white shadow-2xl shadow-slate-300/50 lg:grid-cols-[0.9fr,1.1fr]">
    <div class="relative overflow-hidden bg-slate-950 text-white">
        <div class="soft-grid absolute inset-0 opacity-20"></div>
        <div class="relative h-full p-8 sm:p-10">
            <span class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-200">{{ $panelTitle }}</span>
            <h2 class="mt-6 text-3xl font-bold tracking-tight sm:text-4xl">{{ $title }}</h2>
            @if($subtitle)
                <p class="mt-4 max-w-xl text-sm leading-7 text-slate-300">{{ $subtitle }}</p>
            @endif

            <div class="mt-8 space-y-4">
                @foreach([
                    'Browse and buy polished digital products',
                    'Choose Buyer, Creator, or Affiliate onboarding',
                    'Secure downloads and modern dashboard views',
                ] as $item)
                    <div class="flex items-center gap-3 border-t border-white/10 py-3 text-sm text-slate-200">
                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/10">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 6L9 17l-5-5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        {{ $item }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white p-8 sm:p-10">
        <div class="max-w-xl">
            <h1 class="text-2xl font-bold tracking-tight text-slate-950">{{ $title }}</h1>
            @if($subtitle)
                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $subtitle }}</p>
            @endif
            <div class="mt-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
