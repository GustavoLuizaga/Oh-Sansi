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
    $user = auth()->user();
    $rol = $user->roles->first()->nombre;
    echo $rol;
    switch ($rol) {
        case 'Administrador':
            return view('dashboard');
        case 'Estudiante':
            return view('dashboardEst');
        case 'Tutor':
            return view('dashboardTutor');
        default:
            return view('dashboard'); // Vista por defecto
    }
    //return view('dashboard');
})->middleware(['auth','verified'])->name('dashboard');

Route::get('/servicios', function () {
    return view('servicio');
})->middleware(['auth'])->name('servicios');



require __DIR__.'/auth.php';
require __DIR__.'/areasCategorias.php';
require __DIR__.'/areas.php';
require __DIR__.'/categorias.php';
require __DIR__.'/convocatoria.php';
require __DIR__.'/delegaciones.php';
require __DIR__.'/grados.php';
require __DIR__.'/inscripciones.php';

