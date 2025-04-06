<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreasYCategorias\CategoriaController;


Route::middleware('auth')->prefix('gestionCategorias')->group(function () {
    Route::get('/', [CategoriaController::class, 'index'])->name('categorias.index');
    Route::post('/', [CategoriaController::class, 'store'])->name('categorias.store');
    Route::get('/{id}/edit', [CategoriaController::class, 'edit'])->name('categorias.edit');
    Route::put('/{id}', [CategoriaController::class, 'update'])->name('categorias.update');
    Route::delete('/{id}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
    
});