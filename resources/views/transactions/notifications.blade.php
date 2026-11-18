<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($notifications->isEmpty())
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('No account or transaction notifications.') }}</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($notifications as $notification)
                                <div class="rounded-md border border-gray-200 p-4 dark:border-gray-700">
                                    <p class="text-sm">{{ $notification->message }}</p>
                                    <p class="mt-1 text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }} · {{ $notification->read_at ? 'Read' : 'Unread' }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
