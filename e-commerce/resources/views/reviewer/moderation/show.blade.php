<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="Moderation case #{{ $report->id }}" subtitle="Review the complaint, creator response, and resolution history.">
            <x-slot name="actions"><a href="{{ route('reviewer.moderation.index') }}" class="btn-secondary">Back to reports</a></x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_23rem]">
        <div class="space-y-6">
            <section class="panel">
                <div class="flex flex-wrap items-start justify-between gap-4"><div><p class="eyebrow">Reported content</p><h2 class="mt-1 text-xl font-bold text-slate-950">{{ $report->product->title }}</h2><p class="mt-2 text-sm text-slate-500">Creator: {{ $report->product->creator->name }}</p></div><x-ui.status-badge :status="$report->status" /></div>
                @if($report->review)<div class="mt-5 rounded-2xl bg-slate-50 p-4"><p class="text-sm font-semibold text-slate-900">Review by {{ $report->review->user->name }}</p><p class="mt-2 text-sm text-slate-600">{{ $report->review->body }}</p></div>@endif
            </section>
            <section class="panel"><p class="eyebrow">Complaint</p><p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $report->reason }}</p>@if($report->evidence)<p class="mt-5 text-sm font-semibold text-slate-900">Evidence or reference</p><p class="mt-2 break-words text-sm text-slate-600">{{ $report->evidence }}</p>@endif</section>
            @if($report->counter_notice)<section class="panel border-amber-200 bg-amber-50"><p class="eyebrow text-amber-700">Creator counter-notice</p><p class="mt-3 whitespace-pre-line text-sm leading-7 text-amber-950">{{ $report->counter_notice }}</p></section>@endif
            @if($report->admin_notes)<section class="panel"><p class="eyebrow">Moderator notes</p><p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $report->admin_notes }}</p></section>@endif
        </div>

        <aside class="space-y-5">
            @if(in_array($report->status, ['open', 'countered']))
                <form method="POST" action="{{ route('reviewer.moderation.takedown', $report) }}" class="panel">@csrf<label for="takedown-notes" class="text-sm font-semibold text-slate-900">Takedown reason</label><textarea id="takedown-notes" name="admin_notes" rows="4" class="field-control mt-2" required></textarea><button class="btn-primary mt-4 w-full bg-rose-600 hover:bg-rose-700">Take down content</button></form>
            @endif
            @if(in_array($report->status, ['taken_down', 'countered']))
                <form method="POST" action="{{ route('reviewer.moderation.restore', $report) }}" class="panel">@csrf<label for="restore-notes" class="text-sm font-semibold text-slate-900">Resolution notes</label><textarea id="restore-notes" name="admin_notes" rows="4" class="field-control mt-2" required></textarea><button class="btn-primary mt-4 w-full">Restore content</button></form>
                <form method="POST" action="{{ route('reviewer.moderation.uphold', $report) }}" class="panel">@csrf<label for="uphold-notes" class="text-sm font-semibold text-slate-900">Final decision</label><textarea id="uphold-notes" name="admin_notes" rows="4" class="field-control mt-2" required></textarea><button class="btn-secondary mt-4 w-full">Uphold takedown</button></form>
            @endif
            @if($report->status === 'resolved')<section class="panel text-center"><p class="text-sm text-slate-500">Resolution</p><p class="mt-2 text-lg font-bold text-slate-950">{{ str($report->resolution)->title() }}</p></section>@endif
        </aside>
    </div>
</x-app-layout>
