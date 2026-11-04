<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LibraryController extends Controller
{
    public function index(): View
    {
        $licenses = Auth::user()->licenses()->with('product')->latest()->paginate(12);

        return view('buyer.library.index', compact('licenses'));
    }
}
