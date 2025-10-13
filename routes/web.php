<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Profile Routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Dashboard
    Route::get('/dashboard', \App\Http\Controllers\DashboardController::class)->name('dashboard');

    // Homepage (redirects to dashboard)
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Income Module
    Route::get('incomes', [\App\Http\Controllers\IncomeController::class, 'index'])
        ->name('incomes.index');
    Route::resource('incomes', \App\Http\Controllers\IncomeController::class);

    // Expense Module
    Route::get('expenses/monthly', [\App\Http\Controllers\ExpenseController::class, 'monthly'])
        ->name('expenses.monthly');
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);

    // Budget Module
    Route::resource('budgets', \App\Http\Controllers\BudgetController::class);

    // Budget Rollover Routes
    Route::get('budgets/copy', [\App\Http\Controllers\BudgetController::class, 'showCopyForm'])
        ->name('budgets.copy-form');
    Route::post('budgets/copy', [\App\Http\Controllers\BudgetController::class, 'copyBudgets'])
        ->name('budgets.copy');

    // Settings Routes
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])
        ->name('settings');
    Route::put('/settings/profile', [\App\Http\Controllers\SettingsController::class, 'updateProfile'])
        ->name('settings.profile.update');
    Route::put('/settings/password', [\App\Http\Controllers\SettingsController::class, 'updatePassword'])
        ->name('settings.password.update');
    Route::post('/settings/categories', [\App\Http\Controllers\SettingsController::class, 'storeCategory'])
        ->name('settings.categories.store');
    Route::put('/settings/categories/{category}', [\App\Http\Controllers\SettingsController::class, 'updateCategory'])
        ->name('settings.categories.update');
    Route::delete('/settings/categories/{category}', [\App\Http\Controllers\SettingsController::class, 'destroyCategory'])
        ->name('settings.categories.destroy');

    // Recurring Transactions
    Route::prefix('recurring')->name('recurring.')->group(function () {
        Route::get('/', [\App\Http\Controllers\RecurringTransactionController::class, 'index'])->name('index');
        Route::patch('/{type}/{id}/skip', [\App\Http\Controllers\RecurringTransactionController::class, 'skipNextOccurrence'])->name('skip');
        Route::delete('/{type}/{id}', [\App\Http\Controllers\RecurringTransactionController::class, 'endRecurrence'])->name('end');
        Route::patch('/{type}/{id}/pause', [\App\Http\Controllers\RecurringTransactionController::class, 'pause'])->name('pause');
        Route::patch('/{type}/{id}/resume', [\App\Http\Controllers\RecurringTransactionController::class, 'resume'])->name('resume');
    });
});
