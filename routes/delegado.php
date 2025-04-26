<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Delegado\DelegadoController;

/*
|--------------------------------------------------------------------------
| Rutas para el Delegado
|--------------------------------------------------------------------------
|
| Aquí se registran todas las rutas relacionadas con la gestión de tutores
| por parte de los delegados.
|
*/

Route::middleware(['auth'])->group(function () {
    // Ruta principal para ver la lista de tutores
    Route::get('/delegado', [DelegadoController::class, 'index'])->name('delegado');
    
    // Rutas para gestionar solicitudes de tutores
    Route::get('/delegado/solicitudes', [DelegadoController::class, 'solicitudes'])->name('delegado.solicitudes');
    Route::get('/delegado/solicitudes/{id}', [DelegadoController::class, 'verSolicitud'])->name('delegado.ver-solicitud');
    Route::post('/delegado/solicitudes/{id}/aprobar', [DelegadoController::class, 'aprobarSolicitud'])->name('delegado.aprobar-solicitud');
    Route::post('/delegado/solicitudes/{id}/rechazar', [DelegadoController::class, 'rechazarSolicitud'])->name('delegado.rechazar-solicitud');
    Route::post('/delegado/solicitudes/{id}/toggle-director', [DelegadoController::class, 'toggleDirector'])->name('delegado.toggle-director');
});