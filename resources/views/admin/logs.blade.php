<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Security and audit logs</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-x-auto rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                @if ($logs->isEmpty())
                    <p class="text-sm text-gray-600 dark:text-gray-300">No audit events have been recorded.</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead><tr>
                            <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Time</th>
                            <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Event</th>
                            <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Outcome</th>
                            <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">Actor</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($logs as $log)
                                <tr>
                                    <td class="px-3 py-3 text-sm">{{ $log->created_at->diffForHumans() }}</td>
                                    <td class="px-3 py-3 text-sm">{{ $log->event_type }}</td>
                                    <td class="px-3 py-3 text-sm">{{ $log->outcome }}</td>
                                    <td class="px-3 py-3 text-sm">{{ $log->actor_id ?? 'System' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $logs->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
