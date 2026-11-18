<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="GroveKey is a non-custodial Ethereum Sepolia wallet for a web-based, user-controlled wallet experience.">
    <title>GroveKey - Your keys. Your control.</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[var(--app-bg)] font-sans text-[var(--text-primary)]">
    <div class="relative min-h-screen overflow-hidden">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute left-[-10rem] top-[-12rem] h-96 w-96 rounded-full bg-[var(--brand-tint)] opacity-80"></div>
            <div class="absolute bottom-[-14rem] right-[-10rem] h-[32rem] w-[32rem] rounded-full bg-[var(--brand-soft)] opacity-60"></div>
        </div>

        <header class="relative z-10 mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
            <a href="/" class="flex items-center gap-3" aria-label="GroveKey home">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-[var(--brand-primary)] text-white shadow-sm" aria-hidden="true">
                    <svg viewBox="0 0 32 32" class="h-7 w-7 fill-none" stroke="currentColor" stroke-width="2">
                        <path d="M16 27V14M16 17c-5 0-8-3-8-8 5 0 8 3 8 8Zm0-3c5 0 8-3 8-8-5 0-8 3-8 8Z"/>
                        <circle cx="16" cy="21" r="2.5"/>
                    </svg>
                </span>
                <span>
                    <span class="block text-lg font-bold leading-tight">GroveKey</span>
                    <span class="block text-xs font-medium text-[var(--text-muted)]">Non-Custodial Ethereum Wallet</span>
                </span>
            </a>

            <nav class="flex items-center gap-2" aria-label="Primary navigation">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg bg-[var(--brand-primary)] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[var(--brand-primary-hover)] focus:outline-none focus:ring-4 focus:ring-[var(--focus-ring)]">
                        Open dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-4 py-2.5 text-sm font-semibold text-[var(--text-primary)] hover:bg-[var(--surface-muted)] focus:outline-none focus:ring-4 focus:ring-[var(--focus-ring)]">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-[var(--brand-primary)] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[var(--brand-primary-hover)] focus:outline-none focus:ring-4 focus:ring-[var(--focus-ring)]">
                        Create account
                    </a>
                @endauth
            </nav>
        </header>

        <main class="relative z-10">
            <section class="mx-auto grid min-h-[calc(100vh-92px)] max-w-7xl items-center gap-14 px-6 pb-16 pt-10 lg:grid-cols-[1.08fr_.92fr] lg:px-8">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-[var(--border)] bg-[var(--surface)] px-3 py-1.5 text-sm font-medium text-[var(--text-secondary)] shadow-sm">
                        <span class="h-2 w-2 rounded-full bg-[var(--brand-primary)]"></span>
                        Academic project · Ethereum Sepolia
                    </div>

                    <h1 class="mt-7 max-w-3xl text-5xl font-bold tracking-tight text-[var(--text-primary)] sm:text-6xl lg:text-7xl">
                        Your keys.<br>
                        <span class="text-[var(--brand-primary)]">Your control.</span>
                    </h1>

                    <p class="mt-6 max-w-2xl text-lg leading-8 text-[var(--text-secondary)]">
                        Connect a browser wallet to view public account details, retrieve Sepolia test-ETH balances and prepare transactions with approval controlled entirely by you.
                    </p>

                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        @auth
                            <a href="{{ route('wallet.index') }}" class="inline-flex items-center justify-center rounded-lg bg-[var(--brand-primary)] px-5 py-3 text-base font-semibold text-white shadow-sm hover:bg-[var(--brand-primary-hover)] focus:outline-none focus:ring-4 focus:ring-[var(--focus-ring)]">
                                Connect wallet
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-[var(--brand-primary)] px-5 py-3 text-base font-semibold text-white shadow-sm hover:bg-[var(--brand-primary-hover)] focus:outline-none focus:ring-4 focus:ring-[var(--focus-ring)]">
                                Get started
                            </a>
                        @endauth
                        <a href="#how-it-works" class="inline-flex items-center justify-center rounded-lg border border-[var(--border)] bg-[var(--surface)] px-5 py-3 text-base font-semibold text-[var(--text-primary)] hover:bg-[var(--surface-muted)] focus:outline-none focus:ring-4 focus:ring-[var(--focus-ring)]">
                            How it works
                        </a>
                    </div>

                    <div class="mt-10 flex flex-wrap gap-x-6 gap-y-3 text-sm text-[var(--text-secondary)]">
                        <span class="flex items-center gap-2">
                            <svg viewBox="0 0 20 20" class="h-4 w-4 fill-none text-[var(--brand-primary)]" stroke="currentColor" stroke-width="2"><path d="m5 10 3 3 7-7"/></svg>
                            No seed phrase required
                        </span>
                        <span class="flex items-center gap-2">
                            <svg viewBox="0 0 20 20" class="h-4 w-4 fill-none text-[var(--brand-primary)]" stroke="currentColor" stroke-width="2"><path d="m5 10 3 3 7-7"/></svg>
                            Sepolia test network
                        </span>
                        <span class="flex items-center gap-2">
                            <svg viewBox="0 0 20 20" class="h-4 w-4 fill-none text-[var(--brand-primary)]" stroke="currentColor" stroke-width="2"><path d="m5 10 3 3 7-7"/></svg>
                            User-approved signing
                        </span>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -inset-4 rounded-[2rem] bg-[var(--brand-soft)] opacity-70 blur-2xl" aria-hidden="true"></div>
                    <div class="relative rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-5 shadow-xl shadow-slate-900/5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-[var(--text-muted)]">Ethereum Sepolia</p>
                                <p class="mt-1 font-semibold">GroveKey wallet</p>
                            </div>
                            <span class="rounded-full bg-[var(--brand-soft)] px-3 py-1 text-xs font-semibold text-[var(--brand-primary)]">Testnet</span>
                        </div>

                        <div class="mt-6 rounded-2xl bg-[var(--surface-muted)] p-5">
                            <p class="text-sm text-[var(--text-muted)]">Available balance</p>
                            <p class="mt-2 text-3xl font-bold tabular-nums">0.000 ETH</p>
                            <p class="mt-1 text-xs text-[var(--text-muted)]">Sepolia test ETH · Live value after connection</p>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-xl border border-[var(--border)] p-4">
                                <p class="text-xs text-[var(--text-muted)]">Network</p>
                                <p class="mt-1 text-sm font-semibold">Sepolia</p>
                            </div>
                            <div class="rounded-xl border border-[var(--border)] p-4">
                                <p class="text-xs text-[var(--text-muted)]">Chain ID</p>
                                <p class="mt-1 text-sm font-semibold tabular-nums">11155111</p>
                            </div>
                        </div>

                        <div class="mt-5 rounded-xl border border-[var(--border)] p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs text-[var(--text-muted)]">Security model</p>
                                    <p class="mt-1 text-sm font-semibold">Browser wallet signing</p>
                                </div>
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[var(--brand-soft)] text-[var(--brand-primary)]">
                                    <svg viewBox="0 0 20 20" class="h-5 w-5 fill-none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="8" width="14" height="9" rx="2"/><path d="M6 8V6a4 4 0 0 1 8 0v2"/><circle cx="10" cy="12.5" r="1"/></svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="how-it-works" class="border-t border-[var(--border)] bg-[var(--surface)]">
                <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
                    <p class="text-sm font-semibold uppercase tracking-wider text-[var(--brand-primary)]">How it works</p>
                    <div class="mt-5 grid gap-5 md:grid-cols-3">
                        <article class="rounded-2xl border border-[var(--border)] p-6">
                            <span class="grid h-9 w-9 place-items-center rounded-lg bg-[var(--brand-soft)] text-sm font-bold text-[var(--brand-primary)]">1</span>
                            <h2 class="mt-5 font-semibold">Create an account</h2>
                            <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Use Laravel authentication to protect your GroveKey application account.</p>
                        </article>
                        <article class="rounded-2xl border border-[var(--border)] p-6">
                            <span class="grid h-9 w-9 place-items-center rounded-lg bg-[var(--brand-soft)] text-sm font-bold text-[var(--brand-primary)]">2</span>
                            <h2 class="mt-5 font-semibold">Connect a wallet</h2>
                            <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Approve access in an EIP-1193 browser wallet and confirm the Sepolia network.</p>
                        </article>
                        <article class="rounded-2xl border border-[var(--border)] p-6">
                            <span class="grid h-9 w-9 place-items-center rounded-lg bg-[var(--brand-soft)] text-sm font-bold text-[var(--brand-primary)]">3</span>
                            <h2 class="mt-5 font-semibold">Review and approve</h2>
                            <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Transaction details are reviewed before the trusted browser wallet receives the signing prompt.</p>
                        </article>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
