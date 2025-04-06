<?php

use App\Http\Controllers\ConvocatoriaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Index route
    Route::get('/convocatoria', [ConvocatoriaController::class, 'index'])->name('convocatoria');

    // Nueva Convocatoria routes - ensure these are in the correct order
    Route::get('/convocatoria/crear', [ConvocatoriaController::class, 'create'])->name('convocatorias.crear');
    
    // Make sure this route is correctly defined
    Route::post('/convocatoria/store', [ConvocatoriaController::class, 'store'])->name('convocatorias.store');
    
    // Export routes - move these before the {id} route to prevent conflicts
    Route::get('/convocatoria/export/pdf', [ConvocatoriaController::class, 'exportPdf'])->name('convocatorias.exportPdf');
    Route::get('/convocatoria/export/excel', [ConvocatoriaController::class, 'exportExcel'])->name('convocatorias.exportExcel');
    
    // Edit Convocatoria routes
    Route::get('/convocatoria/{id}/editar', [ConvocatoriaController::class, 'edit'])->name('convocatorias.editar');
    Route::put('/convocatoria/{id}', [ConvocatoriaController::class, 'update'])->name('convocatorias.update');
    
    // View Convocatoria details - this should be last to avoid route conflicts
    Route::get('/convocatoria/{id}', [ConvocatoriaController::class, 'show'])->name('convocatorias.ver');
    
    // Delete Convocatoria
    Route::delete('/convocatoria/{id}', [ConvocatoriaController::class, 'destroy'])->name('convocatorias.eliminar');
});