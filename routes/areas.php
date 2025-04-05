<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/gestionAreas', function () {
        return view('areas y categorias.gestionAreas');
    })->name('gestionAreas');


});
