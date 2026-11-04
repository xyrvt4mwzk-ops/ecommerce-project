<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ $categoryName }}" subtitle="Published products in the {{ strtolower($categoryName) }} category.">
            <x-slot name="actions"><a href="{{ route('categories.index') }}" class="btn-secondary">All categories</a></x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($products as $product)
            <x-ui.product-card :product="$product" :categories="$categories" />
        @empty
            <div class="sm:col-span-2 xl:col-span-3"><x-ui.empty-state title="No published products" message="Products in this category will appear here when creators publish them." action-label="Browse all products" :action-href="route('products.index')" /></div>
        @endforelse
    </div>
    <div class="mt-8">{{ $products->links() }}</div>
</x-app-layout>
