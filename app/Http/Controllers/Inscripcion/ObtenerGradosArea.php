<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GradoCategoria;

class ObtenerGradosArea extends Controller
{
    public function obtenerGradosPorArea($categorias)
    {
        $idsCategoria = collect($categorias)->pluck('idCategoria');

        // Buscar en la tabla gradocategoria todos los grados relacionados con esas categorías
        $grados = GradoCategoria::with('grado')
            ->whereIn('idCategoria', $idsCategoria)
            ->get()
            ->pluck('grado')
            ->unique('idGrado')
            ->values();
    
        return $grados;
    }
}
