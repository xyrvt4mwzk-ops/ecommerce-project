<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\ContentReport;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->value();
        $reports = ContentReport::query()
            ->with(['product.creator', 'review.user', 'reporter', 'reviewer'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('reviewer.moderation.index', compact('reports', 'status'));
    }

    public function show(ContentReport $report): View
    {
        $report->load(['product.creator', 'review.user', 'reporter', 'reviewer']);

        return view('reviewer.moderation.show', compact('report'));
    }

    public function takeDown(Request $request, ContentReport $report): RedirectResponse
    {
        $data = $request->validate(['admin_notes' => ['required', 'string', 'max:5000']]);

        $report->product->update([
            'moderation_status' => Product::MODERATION_TAKEN_DOWN,
            'moderation_notes' => $data['admin_notes'],
            'taken_down_at' => now(),
        ]);
        $report->review?->update(['status' => 'hidden', 'moderation_notes' => $data['admin_notes']]);
        $report->update([
            'reviewer_id' => $request->user()->id,
            'status' => 'taken_down',
            'admin_notes' => $data['admin_notes'],
            'taken_down_at' => now(),
        ]);

        return back()->with('success', 'The reported content has been taken down.');
    }

    public function restore(Request $request, ContentReport $report): RedirectResponse
    {
        $data = $request->validate(['admin_notes' => ['required', 'string', 'max:5000']]);

        $report->product->update([
            'moderation_status' => Product::MODERATION_RESTORED,
            'moderation_notes' => $data['admin_notes'],
            'taken_down_at' => null,
        ]);
        $report->review?->update(['status' => 'published', 'moderation_notes' => $data['admin_notes'], 'flagged_at' => null]);
        $report->update([
            'reviewer_id' => $request->user()->id,
            'status' => 'resolved',
            'resolution' => 'restored',
            'admin_notes' => $data['admin_notes'],
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'The content has been restored and the case resolved.');
    }

    public function uphold(Request $request, ContentReport $report): RedirectResponse
    {
        $data = $request->validate(['admin_notes' => ['required', 'string', 'max:5000']]);
        $report->update([
            'reviewer_id' => $request->user()->id,
            'status' => 'resolved',
            'resolution' => 'removed',
            'admin_notes' => $data['admin_notes'],
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'The takedown has been upheld.');
    }

    public function hideReview(Request $request, Review $review): RedirectResponse
    {
        $data = $request->validate(['moderation_notes' => ['required', 'string', 'max:2000']]);
        $review->update(['status' => 'hidden', 'moderation_notes' => $data['moderation_notes']]);

        return back()->with('success', 'The review has been hidden.');
    }

    public function restoreReview(Review $review): RedirectResponse
    {
        $review->update(['status' => 'published', 'moderation_notes' => null, 'flagged_at' => null]);

        return back()->with('success', 'The review has been restored.');
    }
}
