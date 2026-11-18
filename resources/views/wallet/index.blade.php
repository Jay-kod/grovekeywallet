<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('GroveKey Wallet') }}
        </h2>
    </x-slot>

    <div id="wallet-root"
         data-connect-url="{{ route('wallet.connect') }}"
         class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <section class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" aria-labelledby="wallet-heading">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 id="wallet-heading" class="text-lg font-semibold">{{ __('Browser wallet') }}</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        {{ __('GroveKey never receives or stores private keys or recovery phrases.') }}
                    </p>

                    <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Public address') }}</dt>
                            <dd id="wallet-address" class="mt-1 font-mono" title="">Not connected</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Network') }}</dt>
                            <dd id="wallet-chain" class="mt-1">Unknown</dd>
                        </div>
                    </dl>

                    <p id="wallet-status" class="mt-6 text-sm" role="status" aria-live="polite">
                        {{ __('Checking browser wallet...') }}
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <button id="connect-wallet" type="button"
                                class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                            {{ __('Connect wallet') }}
                        </button>
                        <button id="switch-sepolia" type="button" hidden
                                class="rounded-md border border-amber-600 px-4 py-2 text-sm font-semibold text-amber-700 dark:text-amber-300">
                            {{ __('Switch to Sepolia') }}
                        </button>
                    </div>
                </div>
            </section>

            <section class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" aria-labelledby="saved-wallets-heading">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 id="saved-wallets-heading" class="text-lg font-semibold">{{ __('Saved public wallet links') }}</h3>

                    @if ($walletProfiles->isEmpty())
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">{{ __('No Sepolia wallet is linked to this account.') }}</p>
                    @else
                        <div class="mt-4 space-y-3">
                            @foreach ($walletProfiles as $walletProfile)
                                <div data-wallet-profile class="flex flex-col gap-3 rounded-md border border-gray-200 p-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="font-mono text-sm">{{ $walletProfile->address }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ $walletProfile->network_name }} · Chain ID {{ $walletProfile->chain_id }}</p>
                                    </div>
                                    <button type="button"
                                            data-remove-wallet="{{ route('wallet.destroy', $walletProfile) }}"
                                            class="rounded-md border border-red-600 px-3 py-2 text-sm font-semibold text-red-700 dark:text-red-300">
                                        {{ __('Remove link') }}
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
