<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/inscripciones', function () {
        return view('inscripciones.inscripciones');
    })->name('inscripciones');



});