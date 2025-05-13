<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SharePermissionsWithViews
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $iusIds = collect();
            
            // Obtener todos los permisos (funciones) del usuario a través de sus roles
            if ($user->roles) {
                $iusIds = $user->roles()
                    ->with('funciones')
                    ->get()
                    ->pluck('funciones')
                    ->flatten()
                    ->pluck('idFuncion')
                    ->unique();
            }
            
            // Compartir los permisos con todas las vistas
            View::share('iusIds', $iusIds);
        }

        return $next($request);
    }
} 