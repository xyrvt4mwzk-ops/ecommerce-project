<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header title="{{ $creator->name }}" subtitle="Public creator storefront and published products.">
            <x-slot name="actions"><a href="{{ route('creators.index') }}" class="btn-secondary">All creators</a></x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($products as $product)
            <x-ui.product-card :product="$product" :categories="\App\Models\Product::categories()" />
        @empty
            <div class="sm:col-span-2 xl:col-span-3"><x-ui.empty-state title="No published products" message="This creator has no public products right now." /></div>
        @endforelse
    </div>
    <div class="mt-8">{{ $products->links() }}</div>
</x-app-layout>
