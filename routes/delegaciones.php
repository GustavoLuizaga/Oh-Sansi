<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DelegacionController;

// Add these routes for exports
Route::get('/delegaciones/exportar/pdf', [DelegacionController::class, 'exportPdf'])->name('delegaciones.exportar.pdf');
Route::get('/delegaciones/exportar/excel', [DelegacionController::class, 'exportExcel'])->name('delegaciones.exportar.excel');

Route::middleware('auth')->group(function () {
    Route::get('/delegaciones', [DelegacionController::class, 'index'])->name('delegaciones');
    Route::get('/delegaciones/agregar', [DelegacionController::class, 'create'])->name('delegaciones.agregar');
    Route::post('/delegaciones/store', [DelegacionController::class, 'store'])->name('delegaciones.store');
    Route::get('/delegaciones/{id}', [DelegacionController::class, 'show'])->name('delegaciones.ver');
    Route::get('/delegaciones/{id}/editar', [DelegacionController::class, 'edit'])->name('delegaciones.editar');
    Route::put('/delegaciones/{id}', [DelegacionController::class, 'update'])->name('delegaciones.update');
    Route::delete('/delegaciones/{codigo_sie}/eliminar', [DelegacionController::class, 'destroy'])->name('delegaciones.destroy');
    Route::get('/delegaciones/exportar/pdf', [DelegacionController::class, 'exportPdf'])->name('delegaciones.exportar.pdf');
    Route::get('/delegaciones/exportar/excel', [DelegacionController::class, 'exportExcel'])->name('delegaciones.exportar.excel');
});