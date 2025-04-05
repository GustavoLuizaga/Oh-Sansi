<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/areasCategorias', function () {
        return view('areas y categorias.areasCategorias');
    })->name('areasCategorias');


});