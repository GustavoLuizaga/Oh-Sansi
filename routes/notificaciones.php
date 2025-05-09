<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Notificacion\NotificacionController;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Auth;

Route::get('/notificaciones/nuevas', function () {
    $userId = auth()->id();

    // Retorna las últimas 5 notificaciones del usuario
    $notificaciones = Notificacion::where('user_id', $userId)
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($n) {
            return [
                'mensaje' => $n->mensaje,
                'tiempo' => $n->created_at->diffForHumans()
            ];
        });

    return response()->json($notificaciones);
})->middleware('auth');