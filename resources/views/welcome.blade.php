<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="GroveKey is a secure, non-custodial Ethereum Sepolia wallet interface.">
    <title>GroveKey — Your keys. Your control.</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[var(--app-bg)] font-sans text-[var(--text-primary)]">
    <header class="sticky top-0 z-50 border-b border-[var(--border)]/80 bg-[var(--surface)]/95 backdrop-blur">
        <div class="mx-auto flex h-18 max-w-7xl items-center justify-between px-5 py-3 sm:px-6 lg:px-8">
            <a href="/" class="flex items-center gap-3" aria-label="GroveKey home">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-[var(--brand-primary)] text-white shadow-sm" aria-hidden="true">
                    <svg viewBox="0 0 32 32" class="h-7 w-7 fill-none" stroke="currentColor" stroke-width="2">
                        <path d="M16 27V14M16 17c-5 0-8-3-8-8 5 0 8 3 8 8Zm0-3c5 0 8-3 8-8-5 0-8 3-8 8Z"/>
                        <circle cx="16" cy="21" r="2.5"/>
                    </svg>
                </span>
                <span>
                    <span class="block text-lg font-bold leading-none">GroveKey</span>
                    <span class="mt-1 block text-xs font-medium text-[var(--text-muted)]">Non-custodial wallet</span>
                </span>
            </a>

            <nav class="flex items-center gap-2" aria-label="Primary navigation">
                <a href="#how-it-works" class="hidden rounded-lg px-3 py-2 text-sm font-semibold text-[var(--text-secondary)] hover:bg-[var(--surface-muted)] sm:block">How it works</a>
                <a href="#security" class="hidden rounded-lg px-3 py-2 text-sm font-semibold text-[var(--text-secondary)] hover:bg-[var(--surface-muted)] sm:block">Security</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg bg-[var(--brand-primary)] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[var(--brand-primary-hover)] focus:outline-none focus:ring-4 focus:ring-[var(--focus-ring)]">
                        Open dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-[var(--text-primary)] hover:bg-[var(--surface-muted)]">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-[var(--brand-primary)] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[var(--brand-primary-hover)] focus:outline-none focus:ring-4 focus:ring-[var(--focus-ring)]">
                        Create account
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 -z-10" aria-hidden="true">
                <div class="absolute left-[8%] top-16 h-72 w-72 rounded-full bg-[var(--brand-tint)] blur-3xl"></div>
                <div class="absolute right-[8%] top-28 h-64 w-64 rounded-full bg-[var(--brand-soft)] opacity-70 blur-3xl"></div>
            </div>

            <div class="mx-auto grid max-w-7xl items-center gap-12 px-5 py-16 sm:px-6 sm:py-20 lg:grid-cols-[1.1fr_.9fr] lg:px-8 lg:py-24">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-[var(--border)] bg-[var(--surface)] px-3 py-1.5 text-sm font-medium text-[var(--text-secondary)] shadow-sm">
                        <span class="h-2 w-2 rounded-full bg-[var(--brand-primary)]"></span>
                        Ethereum Sepolia test network
                    </div>

                    <h1 class="mt-7 max-w-3xl text-5xl font-bold leading-[1.05] tracking-tight text-[var(--text-primary)] sm:text-6xl lg:text-7xl">
                        Keep control of<br>
                        <span class="text-[var(--brand-primary)]">your crypto journey.</span>
                    </h1>

                    <p class="mt-7 max-w-2xl text-lg leading-8 text-[var(--text-secondary)]">
                        GroveKey connects to the wallet you already control, so you can check your Sepolia test balance and review Ethereum transactions without sharing your private keys or recovery phrase.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        @auth
                            <a href="{{ route('wallet.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-lg bg-[var(--brand-primary)] px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-[var(--brand-primary-hover)] focus:outline-none focus:ring-4 focus:ring-[var(--focus-ring)]">
                                Connect a browser wallet
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex min-h-12 items-center justify-center rounded-lg bg-[var(--brand-primary)] px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-[var(--brand-primary-hover)] focus:outline-none focus:ring-4 focus:ring-[var(--focus-ring)]">
                                Create your GroveKey account
                            </a>
                        @endauth
                        <a href="#how-it-works" class="inline-flex min-h-12 items-center justify-center rounded-lg border border-[var(--border)] bg-[var(--surface)] px-6 py-3 text-base font-semibold text-[var(--text-primary)] hover:bg-[var(--surface-muted)] focus:outline-none focus:ring-4 focus:ring-[var(--focus-ring)]">
                            See 4 simple steps
                        </a>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm text-[var(--text-secondary)]">
                        <span class="flex items-center gap-2"><span class="grid h-5 w-5 place-items-center rounded-full bg-[var(--brand-soft)] text-xs text-[var(--brand-primary)]">✓</span>No real funds</span>
                        <span class="flex items-center gap-2"><span class="grid h-5 w-5 place-items-center rounded-full bg-[var(--brand-soft)] text-xs text-[var(--brand-primary)]">✓</span>No seed phrase entry</span>
                        <span class="flex items-center gap-2"><span class="grid h-5 w-5 place-items-center rounded-full bg-[var(--brand-soft)] text-xs text-[var(--brand-primary)]">✓</span>You approve every transfer</span>
                    </div>
                </div>

                <div class="relative mx-auto w-full max-w-lg">
                    <div class="absolute -inset-5 rounded-[2rem] bg-[var(--brand-soft)] opacity-60 blur-2xl" aria-hidden="true"></div>
                    <div class="relative overflow-hidden rounded-3xl border border-[var(--border)] bg-[var(--surface)] shadow-2xl shadow-slate-900/10">
                        <div class="flex items-center justify-between border-b border-[var(--border)] px-6 py-5">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)]">Wallet overview</p>
                                <p class="mt-1 font-semibold">Ethereum Sepolia</p>
                            </div>
                            <span class="rounded-full bg-[var(--brand-soft)] px-3 py-1.5 text-xs font-bold text-[var(--brand-primary)]">Connected state</span>
                        </div>

                        <div class="p-6">
                            <div class="rounded-2xl bg-[var(--surface-muted)] p-5">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-[var(--text-muted)]">Test balance</p>
                                    <span class="text-xs font-semibold text-[var(--text-muted)]">Live after connection</span>
                                </div>
                                <p class="mt-3 text-3xl font-bold tabular-nums">Sepolia test ETH</p>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="rounded-xl border border-[var(--border)] p-4">
                                    <p class="text-xs text-[var(--text-muted)]">Public address</p>
                                    <p class="mt-2 truncate font-mono text-sm font-semibold">0x••••••••••••</p>
                                </div>
                                <div class="rounded-xl border border-[var(--border)] p-4">
                                    <p class="text-xs text-[var(--text-muted)]">Chain ID</p>
                                    <p class="mt-2 text-sm font-bold tabular-nums">11155111</p>
                                </div>
                            </div>

                            <div class="mt-4 rounded-xl border border-[var(--border)] p-4">
                                <div class="flex items-start gap-3">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[var(--brand-soft)] text-[var(--brand-primary)]">
                                        <svg viewBox="0 0 20 20" class="h-5 w-5 fill-none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="8" width="14" height="9" rx="2"/><path d="M6 8V6a4 4 0 0 1 8 0v2"/><circle cx="10" cy="12.5" r="1"/></svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold">Review before approval</p>
                                        <p class="mt-1 text-xs leading-5 text-[var(--text-secondary)]">Confirm the recipient, amount, network and estimated fee in the GroveKey review screen before signing in your wallet.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="how-it-works" class="border-y border-[var(--border)] bg-[var(--surface)]">
            <div class="mx-auto max-w-7xl px-5 py-16 sm:px-6 lg:px-8 lg:py-20">
                <div class="max-w-2xl">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[var(--brand-primary)]">Start safely</p>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">What you do, and what GroveKey does</h2>
                    <p class="mt-4 leading-7 text-[var(--text-secondary)]">No setup can be completed on your behalf. GroveKey organizes the application workflow while your browser wallet retains authority over account access and signing.</p>
                </div>

                <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                    <article class="rounded-2xl border border-[var(--border)] bg-[var(--app-bg)] p-6">
                        <span class="grid h-10 w-10 place-items-center rounded-lg bg-[var(--brand-soft)] text-sm font-bold text-[var(--brand-primary)]">1</span>
                        <h3 class="mt-5 font-semibold">Create an account</h3>
                        <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Register with a valid email and password. GroveKey protects this application session.</p>
                    </article>
                    <article class="rounded-2xl border border-[var(--border)] bg-[var(--app-bg)] p-6">
                        <span class="grid h-10 w-10 place-items-center rounded-lg bg-[var(--brand-soft)] text-sm font-bold text-[var(--brand-primary)]">2</span>
                        <h3 class="mt-5 font-semibold">Connect your wallet</h3>
                        <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Choose MetaMask or another compatible browser wallet and approve account access.</p>
                    </article>
                    <article class="rounded-2xl border border-[var(--border)] bg-[var(--app-bg)] p-6">
                        <span class="grid h-10 w-10 place-items-center rounded-lg bg-[var(--brand-soft)] text-sm font-bold text-[var(--brand-primary)]">3</span>
                        <h3 class="mt-5 font-semibold">Check Sepolia</h3>
                        <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">Confirm chain ID <strong class="tabular-nums">11155111</strong>, then view balances and transaction history.</p>
                    </article>
                    <article class="rounded-2xl border border-[var(--border)] bg-[var(--app-bg)] p-6">
                        <span class="grid h-10 w-10 place-items-center rounded-lg bg-[var(--brand-soft)] text-sm font-bold text-[var(--brand-primary)]">4</span>
                        <h3 class="mt-5 font-semibold">Review and sign</h3>
                        <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)]">For a transfer, check every detail and approve in the wallet interface. GroveKey does not hold your keys.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="security" class="bg-[var(--app-bg)]">
            <div class="mx-auto grid max-w-7xl gap-8 px-5 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-20">
                <article class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-7 sm:p-9">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[var(--brand-primary)]">Before you begin</p>
                    <h2 class="mt-3 text-2xl font-bold">Use a dedicated test wallet</h2>
                    <ul class="mt-5 space-y-3 text-sm leading-6 text-[var(--text-secondary)]">
                        <li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[var(--brand-primary)]"></span>Switch the wallet to Ethereum Sepolia.</li>
                        <li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[var(--brand-primary)]"></span>Do not enter a recovery phrase or private key on GroveKey.</li>
                        <li class="flex gap-3"><span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-[var(--brand-primary)]"></span>Use only test ETH. This prototype does not operate on Ethereum mainnet.</li>
                    </ul>
                </article>

                <article class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-7 sm:p-9">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-[var(--brand-primary)]">Transaction safety</p>
                    <h2 class="mt-3 text-2xl font-bold">Never approve blindly</h2>
                    <p class="mt-5 text-sm leading-7 text-[var(--text-secondary)]">Before confirming, verify the recipient address, test-ETH amount, network and fee. A pending transaction is not a confirmed transaction. GroveKey clearly separates submission, confirmation and failure states.</p>
                    @guest
                        <a href="{{ route('register') }}" class="mt-6 inline-flex items-center font-semibold text-[var(--brand-primary)] hover:underline">
                            Create an account to begin <span class="ml-2" aria-hidden="true">→</span>
                        </a>
                    @else
                        <a href="{{ route('wallet.index') }}" class="mt-6 inline-flex items-center font-semibold text-[var(--brand-primary)] hover:underline">
                            Open wallet connection <span class="ml-2" aria-hidden="true">→</span>
                        </a>
                    @endguest
                </article>
            </div>
        </section>
    </main>

    <footer class="border-t border-[var(--border)] bg-[var(--surface)]">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-7 text-sm text-[var(--text-muted)] sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
            <p>GroveKey · Academic non-custodial Ethereum wallet prototype</p>
            <p>Ethereum Sepolia · Chain ID 11155111 · Test assets only</p>
        </div>
    </footer>
</body>
</html>
