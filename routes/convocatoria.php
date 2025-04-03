<?php


Route::middleware('auth')->group(function () {

    Route::get('/convocatoria', function () {
        return view('convocatoria.convocatoria');
    })->name('convocatoria');   


});