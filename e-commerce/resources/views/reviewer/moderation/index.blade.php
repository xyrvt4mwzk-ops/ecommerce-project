<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="DMCA and content reports" subtitle="Investigate reports, manage takedowns, and resolve counter-notices." />
    </x-slot>

    <div class="space-y-6">
        <section class="panel">
            <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label for="status" class="text-sm font-semibold text-slate-800">Case status</label>
                    <select id="status" name="status" class="field-control mt-2">
                        <option value="">All cases</option>
                        @foreach(['open', 'taken_down', 'countered', 'resolved'] as $option)
                            <option value="{{ $option }}" @selected($status === $option)>{{ str($option)->replace('_', ' ')->title() }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn-primary">Filter cases</button>
            </form>
        </section>

        <section class="panel overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600"><tr><th class="px-5 py-4">Content</th><th class="px-5 py-4">Reporter</th><th class="px-5 py-4">Status</th><th class="px-5 py-4">Submitted</th><th class="px-5 py-4"></th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($reports as $report)
                            <tr>
                                <td class="px-5 py-4"><p class="font-semibold text-slate-950">{{ $report->product->title }}</p><p class="mt-1 text-xs text-slate-500">{{ $report->review_id ? 'Reported review' : 'Product copyright report' }}</p></td>
                                <td class="px-5 py-4 text-slate-600">{{ $report->reporter?->name ?? 'Deleted user' }}</td>
                                <td class="px-5 py-4"><x-ui.status-badge :status="$report->status" /></td>
                                <td class="px-5 py-4 text-slate-500">{{ $report->created_at->format('M j, Y H:i') }}</td>
                                <td class="px-5 py-4 text-right"><a href="{{ route('reviewer.moderation.show', $report) }}" class="btn-secondary px-3 py-2 text-xs">Review case</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-14"><x-ui.empty-state title="No moderation cases" message="No reports match the selected status." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($reports->hasPages())<div class="border-t border-slate-100 p-5">{{ $reports->links() }}</div>@endif
        </section>
    </div>
</x-app-layout>
