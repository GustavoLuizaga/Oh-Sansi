<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Delegacion;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $rol = $user->roles->first()->nombre;

        switch ($rol) {
            case 'Administrador':
                $totalDelegaciones = Delegacion::count();
                $convocatorias = \App\Models\Convocatoria::select('idConvocatoria', 'nombre')->get();
                $totalConvocatoriasActivas = \App\Models\Convocatoria::where('estado', 'publicada')->count();
                return view('dashboard', compact('totalDelegaciones', 'convocatorias', 'totalConvocatoriasActivas'));
            case 'Estudiante':
                return view('dashboardEst');
            case 'Tutor':
                return view('dashboardTutor');
            default:
                return view('dashboard', compact('totalDelegaciones')); // Vista por defecto
        }
    }

    public function getDatosPorIdConvocatoria($id)
    {
        $totalEstudiantes = DB::table('inscripcion')
            ->join('tutorestudianteinscripcion', 'inscripcion.idInscripcion', '=', 'tutorestudianteinscripcion.idInscripcion')
            ->where('inscripcion.idConvocatoria', $id)
            ->distinct('tutorestudianteinscripcion.idEstudiante')
            ->count('tutorestudianteinscripcion.idEstudiante');

        // Obtener áreas y la cantidad de estudiantes por área
        $areas = DB::table('detalle_inscripcion')
            ->join('inscripcion', 'detalle_inscripcion.idInscripcion', '=', 'inscripcion.idInscripcion')
            ->join('area', 'detalle_inscripcion.idArea', '=', 'area.idArea')
            ->where('inscripcion.idConvocatoria', $id)
            ->select('area.nombre', DB::raw('count(*) as cantidad'))
            ->groupBy('area.nombre')
            ->orderBy('area.nombre')
            ->get();

        // Formatear para el gráfico
        $labels = $areas->pluck('nombre');
        $data = $areas->pluck('cantidad');


        return response()->json([
            'totalEstudiantes' => $totalEstudiantes,
            'totalTutores' => $this->getTotalTutores($id),
            'areasLabels' => $labels,
            'areasData' => $data,
        ]);
    }

    public function getTotalTutores($id)
    {
        $totalTutores = DB::table('inscripcion')
            ->join('tutorestudianteinscripcion', 'inscripcion.idInscripcion', '=', 'tutorestudianteinscripcion.idInscripcion')
            ->where('inscripcion.idConvocatoria', $id)
            ->distinct('tutorestudianteinscripcion.idTutor')
            ->count('tutorestudianteinscripcion.idTutor');

        return $totalTutores;
    }

    public function getTutores($id)
    {
        $tutores = DB::table('inscripcion')
            ->join('tutorestudianteinscripcion', 'inscripcion.idInscripcion', '=', 'tutorestudianteinscripcion.idInscripcion')
            ->where('inscripcion.idConvocatoria', $id)
            ->distinct('tutorestudianteinscripcion.idTutor')
            ->pluck('tutorestudianteinscripcion.idTutor');

        return $tutores;
    }


    public function getTutoresDelegaciones($id)
    {
        // 1. Obtén los IDs únicos de tutores que participan en la convocatoria usando getTutores
        $tutores = $this->getTutores($id);

        // Si no hay tutores, retorna vacío
        if ($tutores->isEmpty()) {
            return response()->json([
                'labelD' => [],
                'dataD' => [],
            ]);
        }

        // 2. Consulta la distribución por tipo de colegio (dependencia) solo para esos tutores
        $distribucion = DB::table('tutorareadelegacion as tad')
            ->join('delegacion as d', 'tad.idDelegacion', '=', 'd.idDelegacion')
            ->whereIn('tad.id', $tutores) // Solo los tutores que participan en la convocatoria
            ->select('d.dependencia', DB::raw('count(distinct tad.id) as cantidad'))
            ->groupBy('d.dependencia')
            ->orderBy('d.dependencia')
            ->get();

        $labelD = $distribucion->pluck('dependencia');
        $dataD = $distribucion->pluck('cantidad');

        return response()->json([
            'labelD' => $labelD,
            'dataD' => $dataD,
        ]);
    }
}
