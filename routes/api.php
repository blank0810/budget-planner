<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Income API Routes
    Route::get('/incomes/monthly-data', [\App\Http\Controllers\IncomeController::class, 'apiMonthlyData'])
        ->name('api.v1.incomes.monthly');
    
    // Add other API routes here as needed
});
