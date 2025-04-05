<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DelegacionController;

Route::middleware('auth')->group(function () {
    Route::get('/delegaciones', [DelegacionController::class, 'index'])->name('delegaciones');
    Route::get('/delegaciones/agregar', [DelegacionController::class, 'create'])->name('delegaciones.agregar');
    Route::post('/delegaciones/store', [DelegacionController::class, 'store'])->name('delegaciones.store');
});