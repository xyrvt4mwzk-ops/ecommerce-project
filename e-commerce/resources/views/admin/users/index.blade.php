<x-app-layout>
    <x-slot name="header"><x-ui.page-header title="Users" subtitle="Manage marketplace role assignments without exposing private account data publicly." /></x-slot>

    <div class="table-shell">
        <table class="data-table">
            <thead><tr><th>User</th><th>Current role</th><th>Joined</th><th>Assign role</th></tr></thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td><p class="font-semibold text-slate-900">{{ $user->name }}</p><p class="mt-1 text-xs text-slate-500">{{ $user->email }}</p></td>
                        <td>{{ $user->roles->pluck('display_name')->filter()->join(', ') ?: $user->roles->pluck('name')->join(', ') ?: 'Unassigned' }}</td>
                        <td>{{ $user->created_at->format('M j, Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.roles.assign', $user) }}" class="flex max-w-sm gap-2">
                                @csrf
                                <select name="role" class="field-control mt-0" required>
                                    @foreach($roles as $role)<option value="{{ $role->id }}" @selected($user->roles->contains($role))>{{ $role->display_name ?: str($role->name)->replace('-', ' ')->title() }}</option>@endforeach
                                </select>
                                <button class="btn-secondary shrink-0">Save</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-8">{{ $users->links() }}</div>
</x-app-layout>
