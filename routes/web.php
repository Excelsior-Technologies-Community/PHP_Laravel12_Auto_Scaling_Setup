<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScalingController;

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/', [ScalingController::class, 'index'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Load Simulation
|--------------------------------------------------------------------------
*/

Route::get('/simulate/random', [ScalingController::class, 'simulateLoad'])
    ->name('simulate.random');

Route::post('/simulate/custom', [ScalingController::class, 'simulateLoad'])
    ->name('simulate.custom');

Route::get('/simulate/pattern', [ScalingController::class, 'simulateLoadPattern'])
    ->name('simulate.pattern');

/*
|--------------------------------------------------------------------------
| Reset System
|--------------------------------------------------------------------------
*/

Route::post('/reset', [ScalingController::class, 'reset'])
    ->name('reset');

/*
|--------------------------------------------------------------------------
| Metrics API
|--------------------------------------------------------------------------
*/

Route::get('/metrics', [ScalingController::class, 'metrics'])
    ->name('metrics.json');

/*
|--------------------------------------------------------------------------
| Export CSV
|--------------------------------------------------------------------------
*/

Route::get('/export/csv', [ScalingController::class, 'exportCsv'])
    ->name('export.csv');

/*
|--------------------------------------------------------------------------
| Delete History
|--------------------------------------------------------------------------
*/

Route::delete('/history/{id}', [ScalingController::class, 'deleteHistory'])
    ->name('history.delete');

Route::delete('/history', [ScalingController::class, 'deleteAllHistory'])
    ->name('history.deleteAll');
