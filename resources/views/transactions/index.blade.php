<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Transaction history') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($records->isEmpty())
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('No transactions have been submitted from this account.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">{{ __('Transaction') }}</th>
                                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">{{ __('Recipient') }}</th>
                                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">{{ __('Status') }}</th>
                                        <th class="px-3 py-2 text-left text-xs uppercase text-gray-500">{{ __('Submitted') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($records as $record)
                                        <tr>
                                            <td class="px-3 py-3 font-mono text-xs">
                                                <a href="{{ route('transactions.show', $record) }}" class="text-green-700 underline dark:text-green-300">
                                                    {{ substr($record->transaction_hash, 0, 12) }}...
                                                </a>
                                            </td>
                                            <td class="px-3 py-3 font-mono text-xs">{{ substr($record->to_address, 0, 12) }}...</td>
                                            <td class="px-3 py-3 text-sm">{{ ucfirst(str_replace('_', ' ', $record->status)) }}</td>
                                            <td class="px-3 py-3 text-sm">{{ optional($record->submitted_at)->diffForHumans() ?? 'Not recorded' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
