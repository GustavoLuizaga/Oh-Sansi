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

// Incluir rutas de usuarios
require __DIR__.'/usuarios.php';

Route::get('/', function () {
    return view('welcome');
});

// Las rutas de delegado se han movido a delegado.php

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

Route::get('/servicios', [\App\Http\Controllers\ServiceController::class, 'index'])->middleware(['auth'])->name('servicios');
Route::get('/servicios/obtener-funciones-rol/{idRol}', [\App\Http\Controllers\ServiceController::class, 'obtenerFuncionesRol'])->middleware(['auth'])->name('servicios.obtenerFuncionesRol');
Route::get('/servicios/obtener-permisos-disponibles/{idRol}', [\App\Http\Controllers\ServiceController::class, 'obtenerPermisosDisponibles'])->middleware(['auth'])->name('servicios.obtenerPermisosDisponibles');

// Rutas para gestión de roles
Route::post('/servicios/agregar-rol', [\App\Http\Controllers\ServiceController::class, 'agregarRol'])->middleware(['auth'])->name('servicios.agregarRol');
Route::put('/servicios/editar-rol', [\App\Http\Controllers\ServiceController::class, 'editarRol'])->middleware(['auth'])->name('servicios.editarRol');
Route::delete('/servicios/eliminar-rol', [\App\Http\Controllers\ServiceController::class, 'eliminarRol'])->middleware(['auth'])->name('servicios.eliminarRol');

// Rutas para gestión de permisos
Route::post('/servicios/agregar-permiso', [\App\Http\Controllers\ServiceController::class, 'agregarPermiso'])->middleware(['auth'])->name('servicios.agregarPermiso');
Route::post('/servicios/eliminar-permiso', [\App\Http\Controllers\ServiceController::class, 'eliminarPermiso'])->middleware(['auth'])->name('servicios.eliminarPermiso');



// Aquí puedes agregar tus rutas personalizadas
Route::get('/descargar-plantilla-excel', [\App\Http\Controllers\Auth\ResgistrarListaEstController::class, 'descargarPlantilla'])->name('descargar.plantilla.excel');

Route::get('/boleta/preview', [
    App\Http\Controllers\BoletaPago\BoletaDePago::class, 
    'generarOrdenPago'
])->name('boleta.preview');

Route::get('/boleta', [
    App\Http\Controllers\BoletaPago\BoletaDePago::class, 
    'generarOrdenPago'])->name('boleta');;








require __DIR__.'/auth.php';
require __DIR__.'/areasCategorias.php';
require __DIR__.'/areas.php';
require __DIR__.'/categorias.php';
require __DIR__.'/convocatoria.php';
require __DIR__.'/delegaciones.php';
require __DIR__.'/delegado.php';
require __DIR__.'/grados.php';
require __DIR__.'/inscripciones.php';
require __DIR__.'/estudiantes.php';
require __DIR__.'/perfil.php';

