<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Administrator dashboard') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <p class="rounded-md bg-green-100 p-3 text-sm text-green-800 dark:bg-green-950 dark:text-green-300">{{ session('status') }}</p>
            @endif

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800"><p class="text-sm text-gray-500">Application users</p><p class="mt-1 text-2xl font-semibold">{{ $totalUsers }}</p></div>
                <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800"><p class="text-sm text-gray-500">Active users</p><p class="mt-1 text-2xl font-semibold">{{ $activeUsers }}</p></div>
                <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800"><p class="text-sm text-gray-500">Suspended users</p><p class="mt-1 text-2xl font-semibold">{{ $suspendedUsers }}</p></div>
                <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800"><p class="text-sm text-gray-500">Transaction records</p><p class="mt-1 text-2xl font-semibold">{{ $transactionCount }}</p></div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h3 class="text-lg font-semibold">Operational health</h3>
                    <dl class="mt-4 space-y-3">
                        <div class="flex justify-between"><dt>Application</dt><dd>{{ $applicationAvailable ? 'Available' : 'Unavailable' }}</dd></div>
                        <div class="flex justify-between"><dt>Database</dt><dd>{{ $databaseAvailable ? 'Available' : 'Unavailable' }}</dd></div>
                        <div class="flex justify-between"><dt>Blockchain network</dt><dd>{{ $networkName }}</dd></div>
                        <div class="flex justify-between"><dt>Chain ID</dt><dd>{{ $chainId }}</dd></div>
                        <div class="flex justify-between"><dt>RPC browser status</dt><dd>Reported by connected browser wallet</dd></div>
                    </dl>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h3 class="text-lg font-semibold">Non-secret settings</h3>
                    <form method="POST" action="{{ route('admin.settings') }}" class="mt-4 space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="maintenance_notice" class="block text-sm font-medium">Maintenance notice</label>
                            <input id="maintenance_notice" name="maintenance_notice" type="text" maxlength="255"
                                   value="{{ old('maintenance_notice', $settings['maintenance_notice']->setting_value ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
                        </div>
                        <div>
                            <label for="support_status" class="block text-sm font-medium">Support status</label>
                            <select id="support_status" name="support_status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                @foreach (['operational', 'degraded', 'maintenance'] as $status)
                                    <option value="{{ $status }}" @selected(old('support_status', $settings['support_status']->setting_value ?? 'operational') === $status)>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white">Save settings</button>
                    </form>
                </section>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('admin.users') }}" class="rounded-md border border-green-700 px-4 py-2 text-sm font-semibold text-green-700">Manage users</a>
                <a href="{{ route('admin.logs') }}" class="rounded-md border border-gray-400 px-4 py-2 text-sm font-semibold">Review audit logs</a>
            </div>
        </div>
    </div>
</x-app-layout>
