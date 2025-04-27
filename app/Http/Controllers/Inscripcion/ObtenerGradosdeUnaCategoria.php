<?php
namespace App\Http\Controllers\Inscripcion;
use App\Http\Controllers\Controller;

use App\Models\GradoCategoria;

class ObtenerGradosdeUnaCategoria extends Controller
{

public function obtenerGradosPorArea($categoria)
    {
        $idsCategoria = collect([$categoria])->pluck('idCategoria');

        // Buscar en la tabla gradocategoria todos los grados relacionados con esas categorías
        $grados = GradoCategoria::with('grado')
            ->whereIn('idCategoria', $idsCategoria)
            ->get()
            ->pluck('grado')
            ->unique('idGrado')
            ->values();
    
        return $grados;
    }

    public function obtenerGradosPorArea2($categoria)
{
    $idsCategoria = [$categoria];

    $grados = GradoCategoria::with('grado')
        ->whereIn('idCategoria', $idsCategoria)
        ->get()
        ->pluck('grado')
        ->unique('idGrado')
        ->values();
    
    return $grados;
}

}