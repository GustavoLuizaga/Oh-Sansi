<?php



Route::middleware('auth')->group(function () {

    Route::get('/areasCategorias', function () {
        return view('areas.areasCategorias');
    })->name('areasCategorias');


});