<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConvocatoriaAreaCategoria;
use App\Http\Controllers\Inscripcion\ObtenerGradosArea;

class ObtenerCategoriasArea extends Controller
{
    public function categoriasAreas($idConvocatoria) {
        $categorias = ConvocatoriaAreaCategoria::with('categoria')
                ->where('idConvocatoria', $idConvocatoria)
                ->get()
                ->pluck('categoria')
                ->unique('idCategoria')   // Opcional: para evitar duplicados
                ->values();          // Reindexar el array

   
        return $categorias;        
    }
}
