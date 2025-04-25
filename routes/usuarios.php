<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

Route::middleware('auth')->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios');
    Route::get('/usuarios/agregar', [UsuarioController::class, 'create'])->name('usuarios.agregar');
    Route::post('/usuarios/store', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show'])->name('usuarios.ver');
    Route::get('/usuarios/{id}/editar', [UsuarioController::class, 'edit'])->name('usuarios.editar');
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}/eliminar', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
});