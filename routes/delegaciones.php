<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/delegaciones', function () {
        return view('delegaciones.delegaciones');
    })->name('delegaciones');
});