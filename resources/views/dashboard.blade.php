<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div id="wallet-root"
         data-connect-url="{{ route('wallet.connect') }}"
         class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                    <div class="mt-4">
                        <a href="{{ route('wallet.index') }}" class="inline-flex items-center rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                            {{ __('Manage wallet') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold">{{ __('Sepolia wallet') }}</h3>
                    <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Public address') }}</dt>
                            <dd id="wallet-address" class="mt-1 font-mono" title="">Not connected</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('Network') }}</dt>
                            <dd id="wallet-chain" class="mt-1">Unknown</dd>
                        </div>
                    </dl>
                    <div class="mt-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Sepolia test-ETH balance') }}</p>
                        <p id="wallet-balance" class="mt-1 text-2xl font-semibold tabular-nums">Not connected</p>
                        <p id="balance-status" class="mt-1 text-xs text-gray-500">Not retrieved</p>
                    </div>
                    <p id="wallet-status" class="mt-4 text-sm" role="status" aria-live="polite">Checking browser wallet...</p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <button id="connect-wallet" type="button" class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">
                            {{ __('Connect wallet') }}
                        </button>
                        <button id="switch-sepolia" type="button" hidden class="rounded-md border border-amber-600 px-4 py-2 text-sm font-semibold text-amber-700">
                            {{ __('Switch to Sepolia') }}
                        </button>
                        <button id="refresh-balance" type="button" class="rounded-md border border-green-700 px-4 py-2 text-sm font-semibold text-green-700">
                            {{ __('Refresh balance') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
