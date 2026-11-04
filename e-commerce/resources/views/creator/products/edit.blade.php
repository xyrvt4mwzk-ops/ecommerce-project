<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Edit Product"
            subtitle="Update your product information and publishing settings."
        >
            <x-slot name="actions">
                <a href="{{ route('creator.products.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700">Back to products</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="w-full max-w-5xl">
        <div class="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200/70">
            <form method="POST" action="{{ route('creator.products.update', $product) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="title" :value="__('Product title')" />
                        <x-text-input id="title" name="title" type="text" value="{{ old('title', $product->title) }}" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="category" :value="__('Category')" />
                        <select id="category" name="category" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" required>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category', $product->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                    </div>
                </div>

                <div class="grid gap-6 rounded-3xl border border-slate-200 bg-slate-50 p-5 sm:grid-cols-2">
                    <div>
                        <x-input-label for="affiliate_commission_rate" :value="__('Affiliate commission (%)')" />
                        <x-text-input id="affiliate_commission_rate" name="affiliate_commission_rate" type="number" step="0.01" min="0" max="50" value="{{ old('affiliate_commission_rate', $product->affiliate_commission_rate) }}" class="mt-1 block w-full rounded-2xl border-slate-200 bg-white" required />
                        <x-input-error :messages="$errors->get('affiliate_commission_rate')" class="mt-2" />
                    </div>
                    <label class="flex items-center gap-3 self-end rounded-2xl bg-white px-4 py-3 ring-1 ring-slate-200">
                        <input type="hidden" name="affiliate_enabled" value="0" />
                        <input type="checkbox" name="affiliate_enabled" value="1" class="rounded border-slate-300 text-indigo-600" @checked(old('affiliate_enabled', $product->affiliate_enabled)) />
                        <span class="text-sm font-semibold text-slate-800">Allow affiliates to promote this product</span>
                    </label>
                </div>

                <div>
                    <x-input-label for="short_description" :value="__('Short description')" />
                    <textarea id="short_description" name="short_description" rows="3" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50">{{ old('short_description', $product->short_description) }}</textarea>
                    <x-input-error :messages="$errors->get('short_description')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="full_description" :value="__('Full description')" />
                    <textarea id="full_description" name="full_description" rows="6" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50">{{ old('full_description', $product->full_description) }}</textarea>
                    <x-input-error :messages="$errors->get('full_description')" class="mt-2" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="price" :value="__('Price (USD)')" />
                        <x-text-input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price) }}" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" required />
                        <x-input-error :messages="$errors->get('price')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-2xl border-slate-200 bg-slate-50" required>
                            <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $product->status) === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ old('status', $product->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <label class="rounded-[1.5rem] border-2 border-dashed border-slate-300 bg-slate-50 p-5">
                        <x-input-label for="cover_image" :value="__('Cover image')" />
                        <p class="mt-2 text-sm text-slate-600">Replace the existing cover if needed.</p>
                        <input id="cover_image" name="cover_image" type="file" class="mt-4 block w-full text-sm text-slate-500" accept="image/*" />
                        <x-input-error :messages="$errors->get('cover_image')" class="mt-2" />
                    </label>
                    <label class="rounded-[1.5rem] border-2 border-dashed border-slate-300 bg-slate-50 p-5">
                        <x-input-label for="digital_file" :value="__('Digital file')" />
                        <p class="mt-2 text-sm text-slate-600">Replace the downloadable file if needed.</p>
                        <input id="digital_file" name="digital_file" type="file" class="mt-4 block w-full text-sm text-slate-500" accept=".zip,.pdf,.mp3,.mp4,.svg,.png,.jpg,.jpeg" />
                        <x-input-error :messages="$errors->get('digital_file')" class="mt-2" />
                    </label>
                </div>

                <div class="flex justify-end">
                    <x-primary-button class="rounded-2xl px-6 py-3">{{ __('Update Product') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
