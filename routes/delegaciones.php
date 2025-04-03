<?php


Route::middleware('auth')->group(function () {

    Route::get()->name('delegaciones')
        ->get('/delegaciones', function () {
            return view('delegaciones.delegaciones');
        })->name('delegaciones');


});