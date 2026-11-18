# Phase 7 Test Log

**Date:** 2026-11-18
**Timezone:** UTC in the application; local session was UTC+01
**Branch/commit:** Phase 7 integration state
**Build:** Laravel 13.43.0

## Verified automated results

| Check | Result | Evidence |
|---|---|---|
| Full Laravel test suite | Pass | 59 tests, 152 assertions |
| Laravel Pint | Pass | No formatting issues |
| Vite production build | Pass | Assets built successfully |
| Composer audit | Pass | No security advisories |
| npm production audit | Pass | 0 vulnerabilities |
| Migration status | Pass | 10 migrations applied |
| Secret-name search | Pass for schema/source | Only security copy and negative test assertions found |

## Live HTTP smoke checks

| URL | Result |
|---|---|
| `http://127.0.0.1:8000/` | HTTP 200 |
| `http://127.0.0.1:8000/up` | HTTP 200 |
| `http://127.0.0.1:8000/dashboard` | HTTP 302 redirect to `/login` |

## Environment record

| Item | Verified value |
|---|---|
| PHP | 8.5.12 |
| Laravel | 13.43.0 |
| Blade | Laravel Blade |
| Database | SQLite in the configured test/development scaffold |
| Node.js | 22.15.0 |
| npm | 10.9.2 |
| Vite | 8.3.8 |
| PHPUnit | 12.5.44 |
| Ethereum network | Ethereum Sepolia |
| Chain ID | 11155111 |
| Browser wallet | Not available in automated headless tests |
| RPC/balance transaction evidence | Blocked until a browser wallet and funded Sepolia test account are provided |

## Blocked manual gates

The following items require a real browser wallet and Sepolia test account and must not be marked as passed from code alone:

- TC-04 wallet connection
- TC-05 browser-network inspection
- TC-07 live balance
- TC-09 real wallet-signed Sepolia submission
- TC-10 real receipt confirmation
- Genuine Figures 4.1 to 4.6
- Sepolia transaction hash and receipt evidence

No screenshot, balance, transaction hash or confirmation is fabricated.
