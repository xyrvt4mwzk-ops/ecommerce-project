<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="All Products"
            subtitle="Manage marketplace products and update status."
        >
            <x-slot name="actions">
                <a href="{{ route('creator.products.create') }}" class="inline-flex items-center rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Create Product</a>
            </x-slot>
        </x-ui.page-header>
    </x-slot>

    <div class="w-full">
        <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Title</th>
                            <th class="px-4 py-3 text-left font-semibold">Creator</th>
                            <th class="px-4 py-3 text-left font-semibold">Category</th>
                            <th class="px-4 py-3 text-left font-semibold">Price</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-left font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($products as $product)
                            <tr>
                                <td class="px-4 py-4 font-medium text-slate-900">{{ $product->title }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $product->creator->name ?? '—' }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ ucfirst($product->category) }}</td>
                                <td class="px-4 py-4 text-slate-600">${{ number_format($product->price, 2) }}</td>
                                <td class="px-4 py-4"><x-ui.status-badge :status="$product->status" /></td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('creator.products.edit', $product) }}" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-200 hover:text-indigo-700">Edit</a>
                                        <form method="POST" action="{{ route('admin.products.status', $product) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" class="rounded-2xl border-slate-200 text-sm">
                                                @foreach(['draft', 'published', 'archived'] as $status)
                                                    <option value="{{ $status }}" @selected($product->status === $status)>{{ ucfirst($status) }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="rounded-2xl bg-slate-950 px-4 py-2 text-xs font-semibold text-white">Update</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $products->links() }}</div>
        </div>
    </div>
</x-app-layout>
