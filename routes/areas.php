<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreasYCategorias\AreaController;

Route::middleware('auth')->group(function () {

    // Ruta para mostrar la vista con todas las áreas
    Route::get('/gestionAreas', [AreaController::class, 'index'])->name('areas.index');
    
    // Rutas RESTful para las operaciones CRUD
    Route::post('/areas', [AreaController::class, 'store'])->name('areas.store');
    Route::get('/areas/{area}', [AreaController::class, 'show'])->name('areas.show');
    Route::put('/areas/{area}', [AreaController::class, 'update'])->name('areas.update');
    Route::delete('/areas/{area}', [AreaController::class, 'destroy'])->name('areas.destroy');
    Route::get('/areas/{area}/edit', [AreaController::class, 'edit'])->name('areas.edit');
});