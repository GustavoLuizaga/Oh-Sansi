<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
 
    
    public function boot()
    {
        View::composer('layouts.sidebar', function ($view) {
            $iusIds = collect();
    
            if ($user = Auth::user()) {
                $iusIds = $user->roles->flatMap(function ($rol) {
                    return $rol->funciones->flatMap(function ($funcion) {
                        return $funcion->ius->pluck('idIu');
                    });
                })->unique()->values();
            }
    
            $view->with('iusIds', $iusIds);
        });
    }
    
}
