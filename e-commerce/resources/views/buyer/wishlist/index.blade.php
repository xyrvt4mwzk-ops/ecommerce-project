<x-app-layout>
    <x-slot name="header"><x-ui.page-header title="Wishlist" subtitle="Save digital products you want to revisit later." /></x-slot>
    <div class="w-full">
        @if($wishlists->isNotEmpty())
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($wishlists as $wishlist)
                    <div class="relative"><x-ui.product-card :product="$wishlist->product" :categories="\App\Models\Product::categories()" /><form method="POST" action="{{ route('buyer.wishlist.destroy', $wishlist->product) }}" class="absolute right-4 top-4">@csrf @method('DELETE')<button class="rounded-xl bg-white/95 px-3 py-2 text-xs font-semibold text-rose-600 shadow-sm">Remove</button></form></div>
                @endforeach
            </div>
            <div class="mt-8">{{ $wishlists->links() }}</div>
        @else
            <x-ui.empty-state title="No saved products yet" message="Save products from the marketplace to compare or purchase later." action-label="Explore products" :action-href="route('products.index')" />
        @endif
    </div>
</x-app-layout>
