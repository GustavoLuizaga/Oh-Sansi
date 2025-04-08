<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreasYCategorias\AreaCategoriaGradoController;

// Add these routes for exports
Route::get('/areasCategorias/exportar/pdf', [AreaCategoriaGradoController::class, 'exportPdf'])->name('areasCategorias.exportar.pdf');
Route::get('/areasCategorias/exportar/excel', [AreaCategoriaGradoController::class, 'exportExcel'])->name('areasCategorias.exportar.excel');

// Middleware to ensure the user is authenticated
Route::middleware('auth')->group(function () {
    // Ruta para mostrar áreas, categorías y grados
    Route::get('/areasCategorias', [AreaCategoriaGradoController::class, 'index'])
        ->name('areasCategorias');

    // Add these routes for exports
    Route::get('/areasCategorias/exportar/pdf', [AreaCategoriaGradoController::class, 'exportPdf'])->name('areasCategorias.exportar.pdf');
    Route::get('/areasCategorias/exportar/excel', [AreaCategoriaGradoController::class, 'exportExcel'])->name('areasCategorias.exportar.excel');
});