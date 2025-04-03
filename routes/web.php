<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/servicios', function () {
    return view('servicio');
})->middleware(['auth'])->name('servicios');



require __DIR__.'/auth.php';
require __DIR__.'/areas.php';
require __DIR__.'/categorias.php';
require __DIR__.'/convocatoria.php';
require __DIR__.'/delegaciones.php';
require __DIR__.'/grados.php';
require __DIR__.'/incripciones.php';

