<div class="mt-9 flex items-center justify-between gap-4">
    <p class="text-sm text-slate-500"><span class="font-semibold text-slate-900">{{ $products->total() }}</span> {{ \Illuminate\Support\Str::plural('product', $products->total()) }} available</p>
    @if($search || $category)<a href="{{ route('products.index') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-800">Clear filters</a>@endif
</div>

<div class="mt-5 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
    @forelse($products as $product)
        <x-ui.product-card :product="$product" :categories="$categories" />
    @empty
        <div class="sm:col-span-2 xl:col-span-3">
            <x-ui.empty-state title="No products found" message="Try another word, creator, category, or tag." action-label="View all products" :action-href="route('products.index')" />
        </div>
    @endforelse
</div>

<div class="mt-10">{{ $products->links() }}</div>
