<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Send Sepolia test ETH') }}
        </h2>
    </x-slot>

    <div id="transaction-root"
         data-record-url="{{ route('transactions.store') }}"
         data-wallet-profiles='@json($walletProfiles->map(fn ($wallet) => ["id" => $wallet->id, "address" => $wallet->address]))'
         class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <section class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Ethereum Sepolia · Chain ID 11155111
                    </p>

                    <div class="mt-6 space-y-5">
                        <div>
                            <label for="recipient" class="block text-sm font-medium">{{ __('Recipient address') }}</label>
                            <input id="recipient" type="text" autocomplete="off"
                                   class="mt-1 block w-full rounded-md border-gray-300 font-mono shadow-sm dark:border-gray-700 dark:bg-gray-900"
                                   placeholder="0x..." />
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium">{{ __('Amount') }}</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input id="amount" type="text" inputmode="decimal" autocomplete="off"
                                       class="block w-full rounded-l-md border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                                       placeholder="0.001" />
                                <span class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 px-3 text-sm text-gray-500 dark:border-gray-700">
                                    Sepolia test ETH
                                </span>
                            </div>
                        </div>

                        <p id="transaction-status" role="status" aria-live="polite" class="text-sm"></p>

                        <button id="review-transaction" type="button"
                                class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-300">
                            {{ __('Review transaction') }}
                        </button>
                    </div>
                </div>
            </section>

            <section id="transaction-review" hidden class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg" aria-labelledby="review-heading">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 id="review-heading" class="text-lg font-semibold">{{ __('Review and confirm') }}</h3>
                    <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div><dt class="text-sm text-gray-500">Network</dt><dd>Ethereum Sepolia · 11155111</dd></div>
                        <div><dt class="text-sm text-gray-500">Sender</dt><dd id="review-from" class="break-all font-mono text-sm"></dd></div>
                        <div><dt class="text-sm text-gray-500">Recipient</dt><dd id="review-to" class="break-all font-mono text-sm"></dd></div>
                        <div><dt class="text-sm text-gray-500">Amount</dt><dd id="review-amount"></dd></div>
                        <div><dt class="text-sm text-gray-500">Estimated fee</dt><dd id="review-fee">Calculating...</dd></div>
                    </dl>
                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">
                        The final approval occurs in your browser wallet. GroveKey will not receive or use your private key.
                    </p>
                    <button id="approve-transaction" type="button"
                            class="mt-6 rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">
                        {{ __('Confirm in wallet') }}
                    </button>
                </div>
            </section>

            <section id="transaction-result" hidden class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold">{{ __('Submission result') }}</h3>
                    <p id="transaction-result-status" class="mt-2 text-sm"></p>
                    <a id="transaction-explorer" target="_blank" rel="noopener" hidden
                       class="mt-4 inline-block text-sm font-semibold text-green-700 underline dark:text-green-300">
                        View on Sepolia block explorer
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
