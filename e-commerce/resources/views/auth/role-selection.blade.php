<x-app-layout>
    <x-slot name="header">
        <x-ui.page-header
            title="Choose your primary role"
            subtitle="Pick one role to continue. Super-admin is not available here."
        />
    </x-slot>

    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('role.store') }}" class="space-y-8">
            @csrf

            <div class="grid gap-6 lg:grid-cols-3">
                @foreach($roles as $role)
                    <label class="group block cursor-pointer">
                        <input type="radio" name="role" value="{{ $role->name }}" class="peer sr-only" {{ old('role') === $role->name ? 'checked' : '' }}>
                        <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-200/70 transition hover:-translate-y-0.5 hover:shadow-xl peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-fuchsia-600 text-white">
                                        @if($role->name === 'buyer')
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 4h2l2.5 12h10l2-8H7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="20" r="1.5" fill="currentColor"/><circle cx="17" cy="20" r="1.5" fill="currentColor"/></svg>
                                        @elseif($role->name === 'creator')
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 3v18M3 12h18" stroke-width="1.8" stroke-linecap="round"/></svg>
                                        @else
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 19h16M6 19V8l6-3 6 3v11" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @endif
                                    </div>
                                    <h3 class="mt-4 text-xl font-semibold text-slate-900">{{ ucfirst($role->display_name ?? $role->name) }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $role->description }}</p>
                                </div>
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-slate-300 text-transparent peer-checked:border-indigo-500 peer-checked:bg-indigo-500 peer-checked:text-white">✓</span>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            @error('role')
                <p class="text-sm font-medium text-rose-600">{{ $message }}</p>
            @enderror

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Back</a>
                <x-primary-button class="rounded-2xl px-6 py-3">Continue</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
