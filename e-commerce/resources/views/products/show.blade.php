<x-app-layout>
    @php($categoryMap = \App\Models\Product::categories())
    @php($relatedProducts = \App\Models\Product::published()->where('category', $product->category)->whereKeyNot($product->id)->with('creator')->latest()->take(3)->get())
    @php($affiliateReferral = auth()->check() ? $product->affiliateReferrals()->where('affiliate_user_id', auth()->id())->first() : null)

    <x-slot name="header">
        <x-ui.page-header
            title="{{ $product->title }}"
            subtitle="{{ $product->short_description }}"
        >
            <x-slot name="actions">
                <a href="{{ route('products.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700">Back to Catalog</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[1.4fr,0.8fr]">
            <div class="space-y-8">
                <div class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-slate-200/70">
                    <x-ui.product-cover :product="$product" class="h-80" />
                    <div class="space-y-5 p-8">
                        <div class="flex flex-wrap items-center gap-3">
                            <x-ui.status-badge :status="$product->status" />
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ $categoryMap[$product->category] ?? ucwords($product->category) }}</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $product->reviews->isNotEmpty() ? number_format($product->reviews->avg('rating'), 1).' rating' : 'New product' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4 border-t border-slate-200 pt-5 text-sm text-slate-500">
                            <div>
                                <p class="font-medium text-slate-900">{{ $product->creator->name }}</p>
                                <p>Creator profile</p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-semibold tracking-tight text-slate-900">${{ number_format($product->price, 2) }}</p>
                                <p>One-time secure purchase</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                    <x-ui.page-header title="Product description" subtitle="What buyers can expect from this digital product." />
                    <div class="prose prose-slate mt-6 max-w-none">
                        {!! nl2br(e($product->full_description)) !!}
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                        <h3 class="text-lg font-semibold text-slate-900">What’s included</h3>
                        <ul class="mt-4 space-y-3 text-sm text-slate-600">
                            <li>Premium digital file and instant access after purchase</li>
                            <li>Editable source or downloadable package where available</li>
                            <li>Presentation-ready marketplace license record</li>
                        </ul>
                    </div>
                    <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                        <h3 class="text-lg font-semibold text-slate-900">File details</h3>
                        <dl class="mt-4 space-y-3 text-sm text-slate-600">
                            <div class="flex items-center justify-between">
                                <dt>Category</dt>
                                <dd class="font-medium text-slate-900">{{ $categoryMap[$product->category] ?? ucwords($product->category) }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt>Status</dt>
                                <dd class="font-medium text-slate-900">{{ ucfirst($product->status) }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt>Delivery</dt>
                                <dd class="font-medium text-slate-900">Secure download</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-indigo-200">Purchase</p>
                    <p class="mt-4 text-3xl font-semibold tracking-tight">${{ number_format($product->price, 2) }}</p>
                    <p class="mt-3 text-sm leading-6 text-slate-300">Continue to checkout to apply a coupon and choose secure card or KHQR payment.</p>
                    @auth
                        @if(Auth::user()->hasRole('buyer'))
                            <form method="POST" action="{{ route('cart.add', $product) }}" class="mt-6">
                                @csrf
                                <button type="submit" class="btn-secondary w-full">Add to cart</button>
                            </form>
                            <form method="POST" action="{{ route('buyer.wishlist.store', $product) }}" class="mt-3">@csrf<button type="submit" class="btn-ghost w-full border border-white/15 bg-white/5 text-white hover:bg-white/10 hover:text-white">Save to wishlist</button></form>
                            <form method="POST" action="{{ route('cart.add', $product) }}" class="mt-3">
                                @csrf
                                <input type="hidden" name="redirect" value="cart" />
                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950">Checkout</button>
                            </form>
                        @else
                            <div class="mt-6 rounded-2xl bg-white/10 p-4 text-sm text-slate-200">Only buyer accounts can complete purchases. Select the Buyer role to continue.</div>
                        @endif
                    @else
                        <div class="mt-6 space-y-3">
                            <a href="{{ route('login', ['purchase' => $product->slug]) }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950">Sign in to purchase</a>
                            <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-5 py-3 text-sm font-semibold text-white">Sign in to save</a>
                        </div>
                    @endauth
                </div>

                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                    <h3 class="text-lg font-semibold text-slate-900">Creator profile</h3>
                    <div class="mt-4 flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-fuchsia-600 text-lg font-bold text-white">
                            {{ strtoupper(substr($product->creator->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $product->creator->name }}</p>
                            <p class="text-sm text-slate-500">Independent digital creator</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-slate-600">Explore this creator's public products and marketplace storefront.</p>
                    <a href="{{ route('creators.show', $product->creator) }}" class="mt-4 inline-flex text-sm font-semibold text-indigo-700">View storefront <span class="ml-1" aria-hidden="true">→</span></a>
                </div>

                <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
                    <h3 class="text-lg font-semibold text-slate-900">Share this product</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Affiliates can share a referral link and track conversion commissions from the affiliate dashboard.</p>
                    @if($affiliateReferral)
                        <div class="mt-5 rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                            <p class="font-medium text-slate-900">Your referral link</p>
                            <p class="mt-2 break-all font-mono text-xs">{{ route('affiliate.capture', $affiliateReferral->code) }}</p>
                        </div>
                    @else
                        <div class="mt-5 rounded-2xl bg-slate-50 p-4 text-sm text-slate-700">
                            <p class="font-medium text-slate-900">Referral-ready</p>
                            <p class="mt-2">Affiliate users can generate a shareable referral link for this product from their dashboard.</p>
                        </div>
                    @endif
                </div>
            </aside>
        </div>

        <section class="mt-10 rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
            <x-ui.page-header title="Public reviews" subtitle="Feedback from buyers with an active product license." />
            <div class="mt-8 space-y-4">
                @forelse($product->reviews as $review)
                    <article class="rounded-2xl bg-slate-50 p-5">
                        <div class="flex items-center justify-between gap-4"><p class="font-semibold text-slate-900">{{ $review->user->name }}</p><p class="text-sm font-semibold text-amber-600">{{ str_repeat('★', $review->rating) }}<span class="text-slate-300">{{ str_repeat('★', 5 - $review->rating) }}</span></p></div>
                        @if($review->body)<p class="mt-3 text-sm leading-6 text-slate-600">{{ $review->body }}</p>@endif
                        @if($review->creator_response)
                            <div class="mt-4 rounded-xl border border-indigo-100 bg-white p-4"><p class="text-xs font-bold uppercase tracking-wider text-indigo-700">Creator response</p><p class="mt-2 text-sm leading-6 text-slate-700">{{ $review->creator_response }}</p></div>
                        @endif
                        @auth
                            <div class="mt-4 flex flex-wrap gap-3">
                                @if(Auth::id() === $review->user_id || Auth::user()->hasRole('super-admin'))
                                    <form method="POST" action="{{ route('reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review?')">@csrf @method('DELETE')<button class="text-xs font-semibold text-rose-600">Delete review</button></form>
                                @endif
                                @if(Auth::id() === $product->user_id || Auth::user()->hasRole('super-admin'))
                                    <details class="w-full"><summary class="cursor-pointer text-xs font-semibold text-indigo-700">{{ $review->creator_response ? 'Update response' : 'Respond as creator' }}</summary><form method="POST" action="{{ route('creator.reviews.respond', $review) }}" class="mt-3 flex flex-col gap-2 sm:flex-row">@csrf<input name="creator_response" value="{{ $review->creator_response }}" class="field-control mt-0 flex-1" maxlength="2000" required><button class="btn-secondary shrink-0">Save response</button></form></details>
                                @endif
                            </div>
                        @endauth
                    </article>
                @empty
                    <p class="rounded-2xl bg-slate-50 p-5 text-sm text-slate-600">No reviews yet.</p>
                @endforelse
            </div>

            @auth
                @if(Auth::user()->hasRole('buyer'))
                    <form method="POST" action="{{ route('products.reviews.store', $product) }}" class="mt-6 grid gap-3 rounded-2xl border border-slate-200 p-5 sm:grid-cols-[8rem_minmax(0,1fr)_auto]">
                        @csrf
                        <select name="rating" class="field-control mt-0" aria-label="Rating" required>@foreach(range(5, 1) as $rating)<option value="{{ $rating }}">{{ $rating }} stars</option>@endforeach</select>
                        <input name="body" class="field-control mt-0" maxlength="2000" placeholder="Share your experience" />
                        <button class="btn-primary">Write Review</button>
                    </form>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn-secondary mt-6">Sign in to write a review</a>
            @endauth
        </section>

        @auth
            <section class="mt-10 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                <details>
                    <summary class="cursor-pointer text-sm font-semibold text-slate-700">Report copyright infringement (DMCA)</summary>
                    <form method="POST" action="{{ route('content-reports.store', $product) }}" class="mt-5 grid gap-4">
                        @csrf
                        <div><label for="dmca-reason" class="text-sm font-semibold text-slate-900">Explain the infringement</label><textarea id="dmca-reason" name="reason" rows="5" minlength="20" maxlength="5000" class="field-control mt-2" required></textarea></div>
                        <div><label for="dmca-evidence" class="text-sm font-semibold text-slate-900">Evidence or source URL</label><textarea id="dmca-evidence" name="evidence" rows="3" maxlength="5000" class="field-control mt-2"></textarea></div>
                        <button class="btn-secondary justify-self-start">Submit DMCA report</button>
                    </form>
                </details>
            </section>
        @endauth

        <section class="mt-10 rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
            <x-ui.page-header title="Related products" subtitle="More products in the same category." />
            <div class="mt-8 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($relatedProducts as $related)
                    <x-ui.product-card :product="$related" :categories="$categoryMap" />
                @empty
                    <div class="xl:col-span-3">
                        <x-ui.empty-state title="No related products yet" message="More products in this category will appear here as the catalog grows." />
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
