@props([
    'product',
    'categories' => [],
    'showButton' => true,
    'buttonLabel' => 'View product',
])

@php($categoryLabel = $categories[$product->category] ?? ucwords($product->category))

<article {{ $attributes->merge(['class' => 'group overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_30px_rgba(15,23,42,0.04)] transition duration-300 hover:-translate-y-1.5 hover:border-indigo-200 hover:shadow-2xl hover:shadow-indigo-100/70']) }}>
    <a href="{{ route('products.show', $product) }}" class="block">
        <div class="border-b border-slate-100 bg-slate-950 p-2.5 sm:p-3">
            <x-ui.product-cover :product="$product" class="aspect-video rounded-2xl" />
        </div>
        <div class="p-5 sm:p-6">
            <div class="flex items-center justify-between gap-3">
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ $categoryLabel }}</span>
                <span class="text-base font-bold tracking-tight text-slate-950">${{ number_format($product->price, 2) }}</span>
            </div>
            <h3 class="mt-3 line-clamp-1 text-base font-bold tracking-tight text-slate-950 group-hover:text-indigo-700">{{ $product->title }}</h3>
            <p class="mt-2 line-clamp-2 min-h-12 text-sm leading-6 text-slate-600">{{ $product->short_description }}</p>
            <div class="mt-5 flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
                <span class="inline-flex min-w-0 items-center gap-2 text-xs font-medium text-slate-500"><span class="grid h-5 w-5 shrink-0 place-items-center rounded-full bg-slate-100 text-[9px] font-bold text-slate-600">{{ strtoupper(substr($product->creator->name, 0, 1)) }}</span><span class="truncate">{{ $product->creator->name }}</span></span>
                @if($showButton)
                    <span class="shrink-0 text-sm font-semibold text-indigo-700">{{ $buttonLabel }} <span aria-hidden="true">→</span></span>
                @endif
            </div>
        </div>
    </a>
</article>
