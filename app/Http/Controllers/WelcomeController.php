<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Convocatoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    public function index()
    {
        // Obtener áreas únicas de convocatorias publicadas
        $areas = Area::join('convocatoriaareacategoria', 'area.idArea', '=', 'convocatoriaareacategoria.idArea')
            ->join('convocatoria', 'convocatoriaareacategoria.idConvocatoria', '=', 'convocatoria.idConvocatoria')
            ->where('convocatoria.estado', 'Publicada')
            ->select('area.idArea', 'area.nombre')
            ->distinct()
            ->get()
            ->map(function ($area) {
                // Asignar iconos según el nombre del área
                $iconos = [
                    'Física' => 'fas fa-atom',
                    'Química' => 'fas fa-flask',
                    'Matemáticas' => 'fas fa-calculator',
                    'Informática' => 'fas fa-laptop-code',
                    'Robótica' => 'fas fa-robot',
                    'Biología' => 'fas fa-dna',
                    'Astronomía' => 'fas fa-meteor',
                    'Matematica' => 'fas fa-calculator',
                    'Fisica' => 'fas fa-atom',
                    'Quimica' => 'fas fa-flask',
                    'Informatica' => 'fas fa-laptop-code',
                    'Robotica' => 'fas fa-robot',
                    'Biologia' => 'fas fa-dna',
                    'Astronomia' => 'fas fa-meteor'
                ];
                
                $area->icono = $iconos[$area->nombre] ?? 'fas fa-book'; // Icono por defecto
                return $area;
            });

        $hayAreasDisponibles = $areas->isNotEmpty();

        return view('welcome', compact('areas', 'hayAreasDisponibles'));
    }
} 