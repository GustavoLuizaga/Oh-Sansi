<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rol;
use App\Models\Funcion;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function index()
    {
        $roles = Rol::all();
        $funciones = Funcion::all();
        
        // Obtener la primera relación rol-función para mostrar por defecto
        $primerRol = $roles->first();
        $funcionesDelRol = [];
        
        if ($primerRol) {
            $funcionesDelRol = DB::table('rolFuncion')
                ->join('funcion', 'rolFuncion.idFuncion', '=', 'funcion.idFuncion')
                ->where('rolFuncion.idRol', $primerRol->idRol)
                ->select('funcion.*')
                ->get();
        }
        
        return view('servicio', compact('roles', 'funciones', 'primerRol', 'funcionesDelRol'));
    }
    
    public function obtenerFuncionesRol($idRol)
    {
        $funcionesDelRol = DB::table('rolFuncion')
            ->join('funcion', 'rolFuncion.idFuncion', '=', 'funcion.idFuncion')
            ->where('rolFuncion.idRol', $idRol)
            ->select('funcion.*')
            ->get();
            
        return response()->json($funcionesDelRol);
    }
}