@php($editing = isset($coupon))

<form method="POST" action="{{ $editing ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" class="panel p-5 sm:p-7">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div>
            <label for="code" class="text-sm font-semibold text-slate-800">Coupon code</label>
            <input id="code" name="code" required maxlength="50" value="{{ old('code', $coupon->code ?? '') }}" placeholder="WELCOME20" class="field-control uppercase" />
            @error('code')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="type" class="text-sm font-semibold text-slate-800">Discount type</label>
            <select id="type" name="type" required class="field-control">
                <option value="percent" @selected(old('type', $coupon->type ?? 'percent') === 'percent')>Percentage</option>
                <option value="fixed" @selected(old('type', $coupon->type ?? '') === 'fixed')>Fixed amount</option>
            </select>
            @error('type')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="value" class="text-sm font-semibold text-slate-800">Discount value</label>
            <input id="value" name="value" type="number" required min="0.01" step="0.01" value="{{ old('value', $coupon->value ?? '') }}" placeholder="20" class="field-control" />
            @error('value')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="usage_limit" class="text-sm font-semibold text-slate-800">Usage limit <span class="font-normal text-slate-400">(optional)</span></label>
            <input id="usage_limit" name="usage_limit" type="number" min="1" value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}" placeholder="Unlimited" class="field-control" />
            @error('usage_limit')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="creator_id" class="text-sm font-semibold text-slate-800">Creator scope <span class="font-normal text-slate-400">(optional)</span></label>
            <select id="creator_id" name="creator_id" class="field-control">
                <option value="">All creators</option>
                @foreach($creators as $creator)
                    <option value="{{ $creator->id }}" @selected((string) old('creator_id', $coupon->creator_id ?? '') === (string) $creator->id)>{{ $creator->name }} — {{ $creator->email }}</option>
                @endforeach
            </select>
            @error('creator_id')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="product_id" class="text-sm font-semibold text-slate-800">Product scope <span class="font-normal text-slate-400">(optional)</span></label>
            <select id="product_id" name="product_id" class="field-control">
                <option value="">All products</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" @selected((string) old('product_id', $coupon->product_id ?? '') === (string) $product->id)>{{ $product->title }} — {{ $product->creator->name ?? 'Unknown creator' }}</option>
                @endforeach
            </select>
            @error('product_id')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="starts_at" class="text-sm font-semibold text-slate-800">Starts at <span class="font-normal text-slate-400">(optional)</span></label>
            <input id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at', isset($coupon) && $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\\TH:i') : '') }}" class="field-control" />
            @error('starts_at')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="ends_at" class="text-sm font-semibold text-slate-800">Ends at <span class="font-normal text-slate-400">(optional)</span></label>
            <input id="ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at', isset($coupon) && $coupon->ends_at ? $coupon->ends_at->format('Y-m-d\\TH:i') : '') }}" class="field-control" />
            @error('ends_at')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <label class="mt-6 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true)) class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        <span><span class="block text-sm font-semibold text-slate-900">Active coupon</span><span class="mt-1 block text-xs text-slate-500">Buyers can use it only during the configured date window.</span></span>
    </label>

    <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <a href="{{ route('admin.coupons.index') }}" class="btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary">{{ $editing ? 'Save changes' : 'Create coupon' }}</button>
    </div>
</form>
