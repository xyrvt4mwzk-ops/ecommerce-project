@props(['mobile' => false])

@php
    $user = auth()->user();
    $navigation = app(\App\Services\DashboardNavigation::class)->for($user);
    $isMobile = $mobile ? 'true' : 'false';
@endphp

<aside {{ $attributes->merge(['class' => ($mobile ? 'w-[17rem] max-w-[calc(100vw-3rem)] shadow-2xl' : 'w-full') . ' flex h-full min-h-0 flex-col overflow-hidden bg-slate-950 text-white']) }}>
    <div class="flex h-[4.5rem] shrink-0 items-center gap-3 border-b border-white/10 px-4" :class="{ 'lg:justify-center lg:px-2': collapsed && !{{ $isMobile }} }">
        <a href="{{ $navigation['home'] }}" class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 text-xs font-bold text-white shadow-md shadow-indigo-950/30" aria-label="DigitalMarket dashboard">DM</a>
        <div class="min-w-0" :class="{ 'lg:hidden': collapsed && !{{ $isMobile }} }"><p class="truncate text-sm font-bold tracking-tight">DigitalMarket</p><p class="truncate text-xs text-slate-400">{{ $navigation['identity'] }}</p></div>
        @if($mobile)
            <button type="button" @click="open = false" class="ml-auto grid h-9 w-9 place-items-center rounded-xl text-slate-400 transition hover:bg-white/10 hover:text-white" aria-label="Close workspace menu"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m6 6 12 12M18 6 6 18" stroke-width="1.8" stroke-linecap="round"/></svg></button>
        @else
            <button type="button" @click="collapsed = !collapsed; localStorage.setItem('digitalmarket-sidebar-collapsed', collapsed ? '1' : '0')" class="ml-auto hidden h-9 w-9 place-items-center rounded-xl text-slate-400 transition hover:bg-white/10 hover:text-white lg:grid" :class="{ 'lg:ml-0': collapsed }" :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': collapsed }" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m14 7-5 5 5 5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        @endif
    </div>

    <nav class="min-h-0 flex-1 space-y-5 overflow-y-auto overflow-x-hidden px-3 py-5" aria-label="{{ $navigation['role'] }} navigation">
        @foreach($navigation['groups'] as $group)
            <section>
                <p class="px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500" :class="{ 'lg:hidden': collapsed && !{{ $isMobile }} }">{{ $group['label'] }}</p>
                <div class="mt-1.5 space-y-1">
                    @foreach($group['items'] as $item)
                        <a href="{{ $item['href'] }}" @if($mobile) @click="open = false" @endif title="{{ $item['label'] }}" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition focus-visible:ring-white" :class="[{{ $item['active'] ? 'true' : 'false' }} ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white', { 'lg:justify-center lg:px-2': collapsed && !{{ $isMobile }} }]">
                            <x-ui.sidebar-icon :name="$item['icon']" class="shrink-0" />
                            <span class="whitespace-nowrap" :class="{ 'lg:hidden': collapsed && !{{ $isMobile }} }">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </nav>

    @auth
        <div class="shrink-0 border-t border-white/10 p-3">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl p-2 transition hover:bg-white/10" :class="{ 'lg:justify-center': collapsed && !{{ $isMobile }} }">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white/10 text-sm font-bold">{{ str($user->name)->substr(0, 1)->upper() }}</span>
                <span class="min-w-0" :class="{ 'lg:hidden': collapsed && !{{ $isMobile }} }"><span class="block truncate text-sm font-semibold">{{ $user->name }}</span><span class="mt-0.5 inline-flex rounded-md bg-indigo-400/15 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-indigo-200">{{ $navigation['role'] }}</span></span>
            </a>
        </div>
    @endauth
</aside>
