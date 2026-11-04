<x-app-layout>
    @php($workspace = [
        ['label' => 'Overview', 'href' => route('admin.dashboard')],
        ['label' => 'Products', 'href' => route('admin.products.index')],
        ['label' => 'Orders', 'href' => route('admin.orders.index')],
        ['label' => 'Roles & permissions', 'href' => route('admin.roles.index'), 'active' => true],
        ['label' => 'Profile settings', 'href' => route('profile.edit')],
    ])

    <x-slot name="header">
        <x-ui.page-header
            title="Roles & permissions"
            subtitle="Set clear access boundaries for marketplace operations."
        />
    </x-slot>

    <x-ui.dashboard-shell title="Administration" subtitle="Access control" :items="$workspace">
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
            <section class="panel overflow-hidden">
                <div class="flex flex-col gap-2 border-b border-slate-200/80 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <p class="eyebrow">Configured roles</p>
                        <h3 class="mt-1 text-lg font-semibold text-slate-950">Marketplace access</h3>
                    </div>
                    <span class="inline-flex w-fit rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ $roles->count() }} roles</span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($roles as $role)
                        <article class="p-5 sm:p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="font-semibold text-slate-950">{{ $role->display_name ?: str($role->name)->replace('-', ' ')->title() }}</h4>
                                        <span class="rounded-md bg-slate-100 px-2 py-1 font-mono text-[11px] font-medium text-slate-600">{{ $role->name }}</span>
                                    </div>
                                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ $role->description ?: 'No description has been added for this role.' }}</p>
                                </div>
                                <span class="shrink-0 text-sm font-semibold text-slate-500">{{ $role->permissions->count() }} permissions</span>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                @forelse($role->permissions as $permission)
                                    <span class="rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-600">{{ $permission->display_name ?: str($permission->name)->replace('-', ' ')->title() }}</span>
                                @empty
                                    <span class="text-sm text-slate-500">No permissions assigned yet.</span>
                                @endforelse
                            </div>
                        </article>
                    @empty
                        <x-ui.empty-state title="No roles yet" message="Create a role to begin assigning marketplace access." />
                    @endforelse
                </div>
            </section>

            <aside class="panel h-fit p-5 sm:p-6">
                <p class="eyebrow">New role</p>
                <h3 class="mt-1 text-lg font-semibold text-slate-950">Create an access role</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Use a concise internal key and a clear label for administrators.</p>

                <form method="POST" action="{{ route('admin.roles.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label for="role-name" class="text-sm font-semibold text-slate-800">Role key</label>
                        <input id="role-name" name="name" value="{{ old('name') }}" required maxlength="100" placeholder="content-editor" class="mt-2 block w-full rounded-xl border-slate-200 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('name')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="role-display-name" class="text-sm font-semibold text-slate-800">Display name</label>
                        <input id="role-display-name" name="display_name" value="{{ old('display_name') }}" required maxlength="150" placeholder="Content Editor" class="mt-2 block w-full rounded-xl border-slate-200 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('display_name')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="role-description" class="text-sm font-semibold text-slate-800">Description <span class="font-normal text-slate-400">(optional)</span></label>
                        <textarea id="role-description" name="description" rows="3" maxlength="500" placeholder="What this role can manage." class="mt-2 block w-full rounded-xl border-slate-200 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                        @error('description')<p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn-primary w-full">Create role</button>
                </form>
            </aside>
        </div>
    </x-ui.dashboard-shell>
</x-app-layout>
