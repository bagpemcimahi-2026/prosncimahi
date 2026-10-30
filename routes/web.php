<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/data', [DashboardController::class, 'data'])->name('dashboard.data');
Route::post('/verifikasi/{folderId}', [DashboardController::class, 'toggle'])->name('dashboard.verifikasi');
