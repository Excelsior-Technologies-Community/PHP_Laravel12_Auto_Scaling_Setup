<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScalingController;

Route::get('/', [ScalingController::class, 'index'])->name('dashboard');
Route::get('/simulate/random', [ScalingController::class, 'simulateLoad'])->name('simulate.random');
Route::post('/simulate/custom', [ScalingController::class, 'simulateLoad'])->name('simulate.custom');
Route::get('/simulate/pattern', [ScalingController::class, 'simulateLoadPattern'])->name('simulate.pattern');
Route::post('/reset', [ScalingController::class, 'reset'])->name('reset');
Route::get('/metrics', [ScalingController::class, 'metrics'])->name('metrics.json');