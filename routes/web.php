<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\GenealogyController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

// Database Setup for Shared Hosting
Route::get('/setup-database', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return 'Database migration completed successfully.';
    } catch (\Exception $e) {
        return 'Error during migration: ' . $e->getMessage();
    }
});

Route::get('/', function () {
    return view('welcome');
});

// Auth
Route::get('/register', [AuthController::class, 'showRegisterForm']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// User Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Genealogy
    Route::get('/genealogy', [GenealogyController::class, 'index']);
    Route::get('/genealogy/{id}', [GenealogyController::class, 'drillDown']);

    // Financials
    Route::post('/upgrade', [FinancialController::class, 'upgrade']);
    Route::post('/subscribe', [FinancialController::class, 'subscribe']);
    Route::post('/wallet/update', [FinancialController::class, 'updateWallet']);
    Route::post('/withdraw/request', [FinancialController::class, 'requestWithdrawal']);
    Route::post('/withdraw/verify', [FinancialController::class, 'verifyWithdrawal']);

    // Admin Routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::post('/sync', [AdminController::class, 'sync']);
        Route::get('/withdrawals', [AdminController::class, 'pendingWithdrawals'])->name('admin.withdrawals');
        Route::post('/withdrawals/{id}/approve', [AdminController::class, 'approveWithdrawal']);
        Route::post('/withdrawals/{id}/reject', [AdminController::class, 'rejectWithdrawal']);
    });
});
