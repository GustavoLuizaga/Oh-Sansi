<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inscripcion\InscripcionController;
use App\Http\Controllers\Inscripcion\InscripcionEstController;
use App\Http\Controllers\Inscripcion\VerificacionConvocatoriaController;
use App\Http\Controllers\Inscripcion\ObtenerGradosdeUnaCategoria;
use App\Http\Controllers\BoletaPago\BoletaDePago;
use App\Http\Controllers\BoletaPago\BoletaDePagoDeEstudiante;

// Add these routes for exports
Route::get('/inscripciones/estudiante/informacion/exportar/pdf', [BoletaDePagoDeEstudiante::class, 'exportPdf'])->name('inscripcionEstudiante.exportar.pdf');

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

    //Ruta para mostrar el formulario de datos de inscripcion del estudiante
    Route::get('/inscripcion/estudiante/informacion', [InscripcionController::class, 'informacionEstudiante'])
        ->name('inscripcion.estudiante.informacion');

    // Add these routes for exports
    Route::get('/inscripciones/estudiante/informacion/exportar/pdf', [BoletaDePagoDeEstudiante::class, 'exportPdf'])->name('inscripcionEstudiante.exportar.pdf');
        
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
    
    // Rutas para gestión de grupos
    Route::get('/inscripcion/grupos', [\App\Http\Controllers\GrupoController::class, 'index'])
        ->name('inscripcion.grupos');
    Route::post('/inscripcion/grupos', [\App\Http\Controllers\GrupoController::class, 'store'])
        ->name('inscripcion.grupos.store');
    Route::put('/inscripcion/grupos/{id}/status', [\App\Http\Controllers\GrupoController::class, 'updateStatus'])
        ->name('inscripcion.grupos.update-status');
    Route::delete('/inscripcion/grupos/{id}', [\App\Http\Controllers\GrupoController::class, 'destroy'])
        ->name('inscripcion.grupos.destroy');
});
