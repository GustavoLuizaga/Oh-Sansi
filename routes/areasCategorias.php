<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreasYCategorias\AreaCategoriaGradoController;


Route::middleware('auth')->group(function () {
    // Ruta para mostrar áreas, categorías y grados
    Route::get('/areasCategorias', [AreaCategoriaGradoController::class, 'index'])
        ->name('areasCategorias');
});