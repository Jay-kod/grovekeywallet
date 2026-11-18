# GroveKey Wallet

GroveKey is an academic, non-custodial Ethereum Sepolia wallet. Laravel and Blade provide the application interface; private keys and transaction signing remain in the user's browser wallet.

## Local setup

- PHP 8.3 or newer
- Composer 2
- Node.js 22 and npm 10

```powershell
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

## Tests

```powershell
php artisan test
vendor\bin\pint --test
```

## Security boundary

- Never enter or store private keys or recovery phrases.
- Never sign a user's transaction on the Laravel server.
- Enforce Ethereum Sepolia chain ID `11155111`.
