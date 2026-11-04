<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCouponRequest;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(Request $request): View
    {
        $coupons = Coupon::query()
            ->with(['creator', 'product'])
            ->when($request->filled('search'), fn ($query) => $query->where('code', 'like', '%'.$request->string('search')->trim().'%'))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->input('status') === 'active'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        return view('admin.coupons.create', $this->formData());
    }

    public function store(SaveCouponRequest $request): RedirectResponse
    {
        Coupon::create($request->validated());

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.edit', $this->formData() + compact('coupon'));
    }

    public function update(SaveCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($request->validated());

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
    }

    private function formData(): array
    {
        return [
            'creators' => User::query()->whereHas('roles', fn ($query) => $query->where('name', 'creator'))->orderBy('name')->get(['id', 'name', 'email']),
            'products' => Product::query()->with('creator:id,name')->orderBy('title')->get(['id', 'user_id', 'title']),
        ];
    }
}
