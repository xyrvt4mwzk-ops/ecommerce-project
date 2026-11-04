<?php

namespace App\Http\Controllers;

use App\Models\ContentReport;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContentReportController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'review_id' => ['nullable', 'integer', 'exists:reviews,id'],
            'reason' => ['required', 'string', 'min:20', 'max:5000'],
            'evidence' => ['nullable', 'string', 'max:5000'],
        ]);

        $review = isset($data['review_id']) ? Review::findOrFail($data['review_id']) : null;
        abort_if($review && $review->product_id !== $product->id, 422, 'The reported review does not belong to this product.');

        ContentReport::create([
            'product_id' => $product->id,
            'review_id' => $review?->id,
            'reporter_id' => $request->user()->id,
            'type' => 'dmca',
            'reason' => $data['reason'],
            'evidence' => $data['evidence'] ?? null,
            'status' => 'open',
        ]);

        if ($review) {
            $review->update(['flagged_at' => now()]);
        }

        return back()->with('success', 'Your DMCA report has been submitted for review.');
    }

    public function counterNotice(Request $request, ContentReport $report): RedirectResponse
    {
        abort_unless(
            $request->user()->hasRole('super-admin') || $report->product()->where('user_id', $request->user()->id)->exists(),
            403,
        );
        abort_unless(in_array($report->status, ['taken_down', 'open'], true), 422, 'This report cannot accept a counter-notice.');

        $data = $request->validate(['counter_notice' => ['required', 'string', 'min:20', 'max:5000']]);
        $report->update([
            'counter_notice' => $data['counter_notice'],
            'status' => 'countered',
            'countered_at' => now(),
        ]);

        return back()->with('success', 'Your counter-notice has been submitted.');
    }
}
