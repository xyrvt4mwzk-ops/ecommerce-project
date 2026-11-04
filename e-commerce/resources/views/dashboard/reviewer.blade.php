<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Review workspace" subtitle="Investigate product reports, DMCA complaints, takedowns, and counter-notices.">
            <x-slot name="actions"><a href="{{ route('reviewer.moderation.index') }}" class="btn-primary">Open moderation queue</a></x-slot>
        </x-ui.page-header>
    </x-slot>

    <x-ui.dashboard-shell>
        <div class="space-y-6">
            <div class="grid gap-5 sm:grid-cols-3"><x-ui.stat-card label="Open reports" :value="$summary['open']" hint="Awaiting initial review." accent="indigo" /><x-ui.stat-card label="Taken down" :value="$summary['taken_down']" hint="Waiting for resolution." accent="violet" /><x-ui.stat-card label="Counter-notices" :value="$summary['countered']" hint="Require reviewer decision." accent="slate" /></div>
            <section class="panel"><h2 class="text-lg font-bold text-slate-950">Recent moderation cases</h2><div class="mt-4 divide-y divide-slate-100">@forelse($reports as $report)<a href="{{ route('reviewer.moderation.show', $report) }}" class="flex items-center justify-between gap-4 py-4"><div><p class="font-semibold text-slate-950">{{ $report->product->title }}</p><p class="mt-1 text-xs text-slate-500">Reported by {{ $report->reporter?->name ?? 'Deleted user' }}</p></div><x-ui.status-badge :status="$report->status" /></a>@empty<x-ui.empty-state title="No moderation cases" message="New DMCA and review reports will appear here." />@endforelse</div></section>
        </div>
    </x-ui.dashboard-shell>
</x-app-layout>
