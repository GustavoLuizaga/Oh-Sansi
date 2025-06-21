<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $rol = $user->roles->first()->nombre;

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
    }

}
