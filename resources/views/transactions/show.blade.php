<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Transaction details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div><dt class="text-sm text-gray-500">Status</dt><dd class="font-semibold">{{ ucfirst($transactionRecord->status) }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Network</dt><dd>{{ $transactionRecord->network_name }} · {{ $transactionRecord->chain_id }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-sm text-gray-500">Transaction hash</dt><dd class="break-all font-mono text-sm">{{ $transactionRecord->transaction_hash }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-sm text-gray-500">Recipient</dt><dd class="break-all font-mono text-sm">{{ $transactionRecord->to_address }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Amount wei</dt><dd class="font-mono">{{ $transactionRecord->amount_wei }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Block number</dt><dd>{{ $transactionRecord->block_number ?? 'Not available' }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Gas used</dt><dd>{{ $transactionRecord->gas_used ?? 'Not available' }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Last checked</dt><dd>{{ optional($transactionRecord->last_checked_at)?->diffForHumans() ?? 'Not checked' }}</dd></div>
                    </dl>
                    <a href="https://sepolia.etherscan.io/tx/{{ $transactionRecord->transaction_hash }}" target="_blank" rel="noopener"
                       class="mt-6 inline-block text-sm font-semibold text-green-700 underline dark:text-green-300">
                        View on Sepolia block explorer
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
