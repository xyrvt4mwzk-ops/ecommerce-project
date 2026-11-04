<x-app-layout>
    <x-slot name="header"><x-ui.page-header title="Copyright cases" subtitle="Review DMCA reports involving your products and submit counter-notices." /></x-slot>
    <div class="space-y-5">
        @forelse($reports as $report)
            <section class="panel">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"><div><p class="eyebrow">Case #{{ $report->id }}</p><h2 class="mt-1 text-lg font-bold text-slate-950">{{ $report->product->title }}</h2><p class="mt-2 text-sm text-slate-600">{{ $report->reason }}</p></div><x-ui.status-badge :status="$report->status" /></div>
                @if(in_array($report->status, ['open', 'taken_down']) && !$report->counter_notice)
                    <form method="POST" action="{{ route('creator.content-reports.counter', $report) }}" class="mt-5 rounded-2xl bg-slate-50 p-4">@csrf<label for="counter-{{ $report->id }}" class="text-sm font-semibold text-slate-900">Counter-notice</label><textarea id="counter-{{ $report->id }}" name="counter_notice" rows="4" class="field-control mt-2" minlength="20" required></textarea><button class="btn-primary mt-3">Submit counter-notice</button></form>
                @elseif($report->counter_notice)
                    <div class="mt-5 rounded-2xl bg-amber-50 p-4 text-sm text-amber-900"><p class="font-semibold">Counter-notice submitted</p><p class="mt-2">{{ $report->counter_notice }}</p></div>
                @endif
            </section>
        @empty
            <x-ui.empty-state title="No copyright cases" message="Reports involving your products will appear here." />
        @endforelse
        {{ $reports->links() }}
    </div>
</x-app-layout>
