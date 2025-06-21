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


    public function getGradosPorConvocatoria($id)
    {
        $grados = DB::table('detalle_inscripcion as di')
            ->join('inscripcion as i', 'di.idInscripcion', '=', 'i.idInscripcion')
            ->join('grado as g', 'i.idGrado', '=', 'g.idGrado')
            ->where('i.idConvocatoria', $id)
            ->select('g.grado', DB::raw('count(distinct di.idDetalleInscripcion) as cantidad'))
            ->groupBy('g.grado')
            ->orderBy('g.grado')
            ->get();

        $labels = $grados->pluck('grado');
        $data = $grados->pluck('cantidad');

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
    public function getEstudiantesPorConvocatoria($id)
    {
        $totalEstudiantes = DB::table('inscripcion')
            ->join('tutorestudianteinscripcion', 'inscripcion.idInscripcion', '=', 'tutorestudianteinscripcion.idInscripcion')
            ->where('inscripcion.idConvocatoria', $id)
            ->distinct('tutorestudianteinscripcion.idEstudiante')
            ->pluck('tutorestudianteinscripcion.idEstudiante');
        return $totalEstudiantes;
    }

    public function getGeneroEstudiantesPorConvocatoria($id)
    {
        // Obtén los IDs únicos de los estudiantes de la convocatoria
        $estudiantes = $this->getEstudiantesPorConvocatoria($id);

        if ($estudiantes->isEmpty()) {
            return response()->json([
                'masculino' => 0,
                'femenino' => 0,
            ]);
        }

        // Cuenta por género en la tabla users
        $generos = DB::table('users')
            ->whereIn('id', $estudiantes)
            ->select('genero', DB::raw('count(*) as cantidad'))
            ->groupBy('genero')
            ->pluck('cantidad', 'genero');

        return response()->json([
            'masculino' => $generos->get('M', 0),
            'femenino' => $generos->get('F', 0),
        ]);
    }

    public function getTopDelegacionesPorConvocatoria($id)
    {
        // Total de estudiantes únicos en la convocatoria
        $totalEstudiantes = DB::table('inscripcion')
            ->join('tutorestudianteinscripcion', 'inscripcion.idInscripcion', '=', 'tutorestudianteinscripcion.idInscripcion')
            ->where('inscripcion.idConvocatoria', $id)
            ->distinct('tutorestudianteinscripcion.idEstudiante')
            ->count('tutorestudianteinscripcion.idEstudiante');

        // Top 5 delegaciones por cantidad de estudiantes únicos
        $delegaciones = DB::table('inscripcion')
            ->join('tutorestudianteinscripcion', 'inscripcion.idInscripcion', '=', 'tutorestudianteinscripcion.idInscripcion')
            ->join('delegacion', 'inscripcion.idDelegacion', '=', 'delegacion.idDelegacion')
            ->where('inscripcion.idConvocatoria', $id)
            ->select(
                'delegacion.nombre as colegio',
                DB::raw('count(distinct tutorestudianteinscripcion.idEstudiante) as estudiantes')
            )
            ->groupBy('delegacion.nombre')
            ->orderByDesc('estudiantes')
            ->limit(5)
            ->get();

        // Calcula el porcentaje
        $result = $delegaciones->map(function ($item) use ($totalEstudiantes) {
            $item->porcentaje = $totalEstudiantes > 0 ? round(($item->estudiantes / $totalEstudiantes) * 100, 1) : 0;
            return $item;
        });

        return response()->json([
            'total' => $totalEstudiantes,
            'top' => $result
        ]);
    }

    public function getDepartamentosPorConvocatoria($id)
    {
        // Contar estudiantes únicos por departamento de la delegación
        $departamentos = DB::table('inscripcion')
            ->join('tutorestudianteinscripcion', 'inscripcion.idInscripcion', '=', 'tutorestudianteinscripcion.idInscripcion')
            ->join('delegacion', 'inscripcion.idDelegacion', '=', 'delegacion.idDelegacion')
            ->where('inscripcion.idConvocatoria', $id)
            ->select(
                'delegacion.departamento',
                DB::raw('count(distinct tutorestudianteinscripcion.idEstudiante) as estudiantes')
            )
            ->groupBy('delegacion.departamento')
            ->orderBy('delegacion.departamento')
            ->get();

        // Lista de departamentos en el orden que quieres mostrar
        $departamentosLista = [
            'La Paz',
            'Santa Cruz',
            'Cochabamba',
            'Potosí',
            'Chuquisaca',
            'Oruro',
            'Tarija',
            'Beni',
            'Pando'
        ];

        // Armar los datos para el gráfico (0 si no hay estudiantes en ese departamento)
        $data = [];
        foreach ($departamentosLista as $dep) {
            $item = $departamentos->firstWhere('departamento', $dep);
            $data[] = $item ? $item->estudiantes : 0;
        }

        return response()->json([
            'labels' => $departamentosLista,
            'data' => $data,
        ]);
    }

    public function getTopTutoresPorConvocatoria($id)
    {
        // Total de estudiantes únicos en la convocatoria (para porcentaje)
        $totalEstudiantes = DB::table('inscripcion')
            ->join('tutorestudianteinscripcion', 'inscripcion.idInscripcion', '=', 'tutorestudianteinscripcion.idInscripcion')
            ->where('inscripcion.idConvocatoria', $id)
            ->distinct('tutorestudianteinscripcion.idEstudiante')
            ->count('tutorestudianteinscripcion.idEstudiante');

        // Top 5 tutores con más estudiantes inscritos en la convocatoria
        $tutores = DB::table('tutorestudianteinscripcion as tei')
            ->join('inscripcion as i', 'tei.idInscripcion', '=', 'i.idInscripcion')
            ->join('tutor as t', 'tei.idTutor', '=', 't.id')
            ->join('users as u', 't.id', '=', 'u.id')
            ->where('i.idConvocatoria', $id)
            ->select(
                't.id as idTutor',
                DB::raw('CONCAT(u.name, " ", u.apellidoPaterno, " ", u.apellidoMaterno) as nombre'),
                DB::raw('count(distinct tei.idEstudiante) as estudiantes')
            )
            ->groupBy('t.id', 'u.name', 'u.apellidoPaterno', 'u.apellidoMaterno')
            ->orderByDesc('estudiantes')
            ->limit(5)
            ->get();

        // Para cada tutor, obtener sus áreas (materias)
        foreach ($tutores as $tutor) {
            $areas = DB::table('tutorareadelegacion as tad')
                ->join('area as a', 'tad.idArea', '=', 'a.idArea')
                ->where('tad.id', $tutor->idTutor)
                ->where('tad.idConvocatoria', $id)
                ->pluck('a.nombre');
            $tutor->areas = $areas;
            // Porcentaje respecto al total de estudiantes
            $tutor->porcentaje = $totalEstudiantes > 0 ? round(($tutor->estudiantes / $totalEstudiantes) * 100, 1) : 0;
        }

        return response()->json([
            'top' => $tutores
        ]);
    }
}
