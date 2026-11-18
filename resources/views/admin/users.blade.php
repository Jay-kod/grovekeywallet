<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">User accounts</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-x-auto rounded-lg bg-white shadow-sm dark:bg-gray-800">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead><tr>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Name</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Email</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Role</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs uppercase text-gray-500">Action</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-4 py-3">{{ $user->name }}</td>
                                <td class="px-4 py-3">{{ $user->email }}</td>
                                <td class="px-4 py-3">{{ $user->role }}</td>
                                <td class="px-4 py-3">{{ $user->status }}</td>
                                <td class="px-4 py-3">
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.status', $user) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'suspended' : 'active' }}" />
                                            <button type="submit" class="text-sm font-semibold text-green-700">{{ $user->status === 'active' ? 'Suspend' : 'Activate' }}</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-500">Current administrator</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
