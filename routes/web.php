<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/connect', [WalletController::class, 'store'])->name('wallet.connect');
    Route::delete('/wallet/{walletProfile}', [WalletController::class, 'destroy'])->name('wallet.destroy');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transactionRecord}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::post('/transactions/{transactionRecord}/reconcile', [TransactionController::class, 'reconcile'])->name('transactions.reconcile');
    Route::get('/notifications', [TransactionController::class, 'notifications'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [TransactionController::class, 'markNotificationRead'])->name('notifications.read');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/status', [AdminController::class, 'updateStatus'])->name('users.status');
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
    Route::patch('/settings', [AdminController::class, 'settings'])->name('settings');
});

require __DIR__.'/auth.php';
