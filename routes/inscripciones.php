<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inscripcion\InscripcionController;

Route::middleware('auth')->group(function () {
    // Main inscripciones view
    Route::get('/inscripciones', function () {
        return view('inscripciones.inscripciones');
    })->name('inscripciones');

    // Student registration routes
    Route::get('/inscripcion/estudiante', [InscripcionController::class, 'index'])
        ->name('inscripcion.estudiante');
    
    Route::post('/inscripcion/estudiante/store', [InscripcionController::class, 'store'])
        ->name('inscripcion.store');

    // Tutor registration routes
    Route::get('/inscripcion/tutor', function () {
        return view('inscripciones.inscripcionTutor');
    })->name('inscripcion.tutor');
    
    Route::post('/inscripcion/tutor/store', [InscripcionController::class, 'storeTutor'])
        ->name('inscripcion.tutor.store');
});