<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inscripcion\InscripcionController;
use App\Http\Controllers\Inscripcion\InscripcionEstController;
use App\Http\Controllers\Inscripcion\VerificacionConvocatoriaController;
use App\Http\Controllers\Inscripcion\ObtenerGradosdeUnaCategoria;

Route::middleware('auth')->group(function () {
    // Main inscripciones view
    Route::get('/inscripciones', function () {
        return view('inscripciones.inscripciones');
    })->name('inscripciones');

    // Student registration routes
    Route::get('/inscripcion/estudiante', [InscripcionController::class, 'index'])
        ->name('inscripcion.estudiante');
    
    Route::post('/inscripcion/estudiante/manual/store', [InscripcionController::class, 'storeManual'])
        ->name('inscripcion.estudiante.manual.store');    

    Route::post('/inscripcion/estudiante/store', [InscripcionEstController::class, 'store'])
        ->name('inscripcion.store');

    // Tutor registration routes
    Route::get('/inscripcion/tutor', [InscripcionController::class, 'showTutorProfile'])
        ->name('inscripcion.tutor');

    Route::post('/inscripcion/tutor/store', [InscripcionController::class, 'storeTutor'])
        ->name('inscripcion.tutor.store');

        
    // Ruta para mostrar el formulario de carga
    Route::get('/register-lista', [App\Http\Controllers\Auth\ResgistrarListaEstController::class, 'index'])
        ->name('register.lista');

    // Ruta para procesar el archivo Excel
    Route::post('/register-lista', [App\Http\Controllers\Auth\ResgistrarListaEstController::class, 'store'])
        ->name('register.lista.store');

    Route::get('/verDatosCovocatoria', [VerificacionConvocatoriaController::class, 'mostrarAreasCategoriasGrados']);
    
    Route::get('/obtener-categorias/{idConvocatoria}/{idArea}', 
    [App\Http\Controllers\Inscripcion\ObtenerCategoriasArea::class, 'categoriasAreas2'])
    ->name('obtener.categorias');



    Route::get('/obtener-grados/{idCategoria}', [ObtenerGradosdeUnaCategoria::class, 'obtenerGradosPorArea2']);
    
});
