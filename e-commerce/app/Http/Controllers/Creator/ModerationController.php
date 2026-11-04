<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\ContentReport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function index(Request $request): View
    {
        $reports = ContentReport::query()
            ->whereHas('product', fn ($query) => $query->where('user_id', $request->user()->id))
            ->with(['product', 'review.user', 'reporter', 'reviewer'])
            ->latest()->paginate(15);

        return view('creator.moderation.index', compact('reports'));
    }
}
