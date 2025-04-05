<?php

use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {
    Route::get('/gestionCategorias', function () {
        return view('areas y categorias.gestionCategorias');
    })->name('gestionCategorias');
});
