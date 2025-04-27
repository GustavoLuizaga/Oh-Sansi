<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inscripcion\InscripcionEstController;
use App\Http\Controllers\Inscripcion\VerificacionConvocatoriaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Remove from the auth:sanctum group for testing
Route::get('/validate-tutor-token/{token}', [InscripcionEstController::class, 'validateTutorToken']);
Route::get('/categoria/{id}/grados', [InscripcionEstController::class, 'getGradosByCategoria']);
Route::get('/convocatoria/{idConvocatoria}/area/{idArea}/categorias', [InscripcionEstController::class, 'getCategoriasByAreaConvocatoria']);
