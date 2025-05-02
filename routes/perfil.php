<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerfilController;

Route::middleware(['auth'])->group(function () {
    // Ruta para mostrar el perfil
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
    
    // Ruta para actualizar la información del perfil
    Route::put('/perfil/update', [PerfilController::class, 'update'])->name('perfil.update');
    
    // Ruta para actualizar la contraseña
    Route::put('/perfil/update-password', [PerfilController::class, 'updatePassword'])->name('perfil.update-password');
});