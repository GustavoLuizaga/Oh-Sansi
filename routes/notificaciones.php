<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Notificacion\NotificacionController;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Auth;

Route::get('/notificaciones/nuevas', function () {
    $userId = Auth::user()->id;

    // Retorna las últimas 5 notificaciones del usuario
    $notificaciones = Notificacion::where('user_id', $userId)
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($n) {
            return [
                'mensaje' => $n->mensaje,
                'tipo' => $n->tipo,
                'tiempo' => $n->created_at->diffForHumans()
            ];
        });

    return response()->json($notificaciones);
})->middleware('auth');

Route::get('/notificaciones/todas', function () {
    $userId = auth()->id();

    // Retorna todas las notificaciones del usuario
    $notificaciones = Notificacion::where('user_id', $userId)
        ->latest()
        ->get()
        ->map(function ($n) {
            return [
                'id' => $n->id,
                'mensaje' => $n->mensaje,
                'tipo' => $n->tipo,
                'tiempo' => $n->created_at->diffForHumans()
            ];
        });

    return response()->json($notificaciones);
})->middleware('auth');

Route::delete('/notificaciones/borrar/{id}', function ($id) {
    $userId = auth()->id();
    $notificacion = \App\Models\Notificacion::where('id', $id)->where('user_id', $userId)->first();

    if ($notificacion) {
        $notificacion->delete();
        return response()->json(['success' => true]);
    } else {
        return response()->json(['success' => false, 'message' => 'No encontrada o no autorizada'], 404);
    }
})->middleware('auth');
