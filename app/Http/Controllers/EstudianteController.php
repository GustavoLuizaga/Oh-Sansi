<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\User;
use App\Models\Inscripcion;
use App\Models\Convocatoria;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Delegacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class EstudianteController extends Controller
{
    /**
     * Muestra la lista de estudiantes registrados
     */
    public function index(Request $request)
    {
        // Obtener el usuario actual
        $user = auth()->user();
        
        // Verificar si el usuario es tutor
        $esTutor = $user->roles->contains('idRol', 2);
    
        // Log para debug
        Log::info('Verificando rol de usuario:', ['esTutor' => $esTutor]);
    
        // Inicializar la consulta base
        $estudiantesQuery = Estudiante::with(['user', 'inscripciones.delegacion', 'inscripciones.area', 'inscripciones.categoria']);
    
        if ($esTutor) {
            // Lógica para tutores
            $tutor = $user->tutor;
            $delegacionId = $tutor->primerIdDelegacion();
    
            Log::info('Consultando estudiantes para delegación (tutor):', ['delegacionId' => $delegacionId]);
    
            $estudiantesQuery->whereHas('tutores')
                ->whereHas('inscripciones', function ($query) use ($delegacionId) {
                    $query->where('idDelegacion', $delegacionId)
                        ->where('status', 'aprobado');
                })
                ->orWhere(function ($query) use ($delegacionId) {
                    $query->whereHas('tutores')
                        ->whereDoesntHave('inscripciones');
                });
        } else {
            // Lógica para otros usuarios
            Log::info('Consultando todos los estudiantes');
    
            $estudiantesQuery->whereHas('inscripciones', function ($query) {
                $query->where('status', 'aprobado');
            });
    
            // Filtrar por delegación si se especifica
            if ($request->filled('delegacion')) {
                $estudiantesQuery->whereHas('inscripciones', function ($query) use ($request) {
                    $query->where('idDelegacion', $request->delegacion);
                });
            }
        }
    
        // Aplicar filtros de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $estudiantesQuery->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('apellidoPaterno', 'like', "%{$search}%")
                    ->orWhere('apellidoMaterno', 'like', "%{$search}%")
                    ->orWhere('ci', 'like', "%{$search}%");
            });
        }
    
        // Aplicar filtros adicionales solo si es tutor
        if ($esTutor) {
            $delegacionId = $user->tutor->primerIdDelegacion();
            
            if ($request->filled('convocatoria')) {
                $estudiantesQuery->whereHas('inscripciones', function ($query) use ($request, $delegacionId) {
                    $query->where('idConvocatoria', $request->convocatoria)
                        ->where('idDelegacion', $delegacionId);
                });
            }
    
            if ($request->filled('area')) {
                $estudiantesQuery->whereHas('inscripciones', function ($query) use ($request, $delegacionId) {
                    $query->where('idArea', $request->area)
                        ->where('idDelegacion', $delegacionId);
                });
            }
    
            if ($request->filled('categoria')) {
                $estudiantesQuery->whereHas('inscripciones', function ($query) use ($request, $delegacionId) {
                    $query->where('idCategoria', $request->categoria)
                        ->where('idDelegacion', $delegacionId);
                });
            }
        }
    
        // Obtener estudiantes paginados
        $estudiantes = $estudiantesQuery->paginate(10);
    
        // Log del resultado
        Log::info('Estudiantes encontrados:', [
            'total' => $estudiantes->total(),
            'pagina_actual' => $estudiantes->currentPage(),
            'por_pagina' => $estudiantes->perPage(),
            'esTutor' => $esTutor
        ]);
    
        // Obtener datos para los filtros
        $convocatorias = Convocatoria::all();
        $areas = Area::all();
        $categorias = Categoria::all();
        $delegaciones = Delegacion::all();
    
        return view('inscripciones.listaEstudiantes', compact(
            'estudiantes',
            'convocatorias',
            'areas',
            'categorias',
            'delegaciones',
            'esTutor'
        ));
    }

    /**
     * Muestra la lista de estudiantes con inscripciones pendientes
     */
    public function pendientes(Request $request)
{
    // Obtener el usuario actual
    $user = auth()->user();
    
    // Verificar si el usuario es tutor (rol con ID 2)
    $esTutor = $user->roles->contains('idRol', 2);

    // Log para debug
    Log::info('Verificando rol de usuario:', ['esTutor' => $esTutor]);

    // Inicializar la consulta base
    $estudiantesQuery = Estudiante::with(['user', 'inscripciones.delegacion', 'inscripciones.area', 'inscripciones.categoria']);

    if ($esTutor) {
        // Lógica para tutores
        $tutor = $user->tutor;
        $delegacionId = $tutor->primerIdDelegacion();

        Log::info('Consultando estudiantes para delegación (tutor):', ['delegacionId' => $delegacionId]);

        $estudiantesQuery->whereHas('tutores')
            ->whereHas('inscripciones', function ($query) use ($delegacionId) {
                $query->where('idDelegacion', $delegacionId)
                    ->where('status', 'pendiente');
            })
            ->orWhere(function ($query) use ($delegacionId) {
                $query->whereHas('tutores')
                    ->whereDoesntHave('inscripciones');
            });
    } else {
        // Lógica para otros usuarios
        Log::info('Consultando todos los estudiantes con inscripciones pendientes');

        $estudiantesQuery->whereHas('inscripciones', function ($query) {
            $query->where('status', 'pendiente');
        });

        // Filtrar por delegación si se especifica
        if ($request->filled('delegacion')) {
            $estudiantesQuery->whereHas('inscripciones', function ($query) use ($request) {
                $query->where('idDelegacion', $request->delegacion);
            });
        }
    }

    // Aplicar filtros de búsqueda
    if ($request->filled('search')) {
        $search = $request->search;
        $estudiantesQuery->whereHas('user', function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('apellidoPaterno', 'like', "%{$search}%")
                ->orWhere('apellidoMaterno', 'like', "%{$search}%")
                ->orWhere('ci', 'like', "%{$search}%");
        });
    }

    // Obtener estudiantes paginados
    $estudiantes = $estudiantesQuery->paginate(10);

    // Log del resultado
    Log::info('Estudiantes encontrados:', [
        'total' => $estudiantes->total(),
        'esTutor' => $esTutor
    ]);

    // Obtener datos para los filtros
    $convocatorias = Convocatoria::all();
    $areas = Area::all();
    $categorias = Categoria::all();
    $delegaciones = Delegacion::all();
    
    // Definir las modalidades disponibles
    $modalidades = ['individual', 'duo', 'equipo'];

    return view('inscripciones.listaEstudiantesPendientes', compact(
        'estudiantes',
        'convocatorias',
        'areas',
        'categorias',
        'delegaciones',
        'esTutor',
        'modalidades'
    ));
}
    /**
     * Muestra los detalles de un estudiante
     */
    public function show($id)
    {
        $estudiante = Estudiante::with([
            'user',
            'inscripciones.delegacion',
            'inscripciones.area',
            'inscripciones.categoria',
            'inscripciones.convocatoria',
            'inscripciones.grado',
            'tutores.user'
        ])
            ->findOrFail($id);

        return view('inscripciones.verEstudiante', compact('estudiante'));
    }

    /**
     * Exporta la lista de estudiantes a PDF
     */
    public function exportPdf(Request $request)
    {
        // Implementar exportación a PDF
        // Similar a la función index pero retornando un PDF
        return redirect()->back()->with('success', 'Exportación a PDF en desarrollo');
    }

    /**
     * Exporta la lista de estudiantes a Excel
     */
    public function exportExcel(Request $request)
    {
        // Implementar exportación a Excel
        // Similar a la función index pero retornando un Excel
        return redirect()->back()->with('success', 'Exportación a Excel en desarrollo');
    }

    /**
     * Elimina un estudiante
     */
    public function destroy($id)
    {
        try {
            $estudiante = Estudiante::findOrFail($id);
            $userId = $estudiante->id;

            // Eliminar el estudiante
            $estudiante->delete();

            // Eliminar el usuario asociado
            User::destroy($userId);

            return redirect()->route('estudiantes.lista')->with('deleted', 'true');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el estudiante: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para completar la inscripción de un estudiante pendiente
     */
    public function completarInscripcion($id)
    {
        $estudiante = Estudiante::with('user')->findOrFail($id);

        // Obtener datos para los selects del formulario
        $convocatorias = Convocatoria::where('estado', 'Activo')->get();
        $areas = Area::all();
        $categorias = Categoria::all();
        $grados = \App\Models\Grado::all();
        $delegaciones = Delegacion::all();

        return view('inscripciones.completarInscripcion', compact(
            'estudiante',
            'convocatorias',
            'areas',
            'categorias',
            'grados',
            'delegaciones'
        ));
    }

    /**
     * Procesa la inscripción completa de un estudiante pendiente
     */
    public function storeCompletarInscripcion(Request $request, $id)
    {
        try {
            $request->validate([
                'idConvocatoria' => 'required|exists:convocatoria,idConvocatoria',
                'idArea' => 'required|exists:area,idArea',
                'idCategoria' => 'required|exists:categoria,idCategoria',
                'idGrado' => 'required|exists:grado,idGrado',
                'idDelegacion' => 'required|exists:delegacion,idDelegacion',
                'numeroContacto' => 'required|string|max:8',
            ]);

            $estudiante = Estudiante::findOrFail($id);

            // Crear la inscripción
            $inscripcion = Inscripcion::create([
                'fechaInscripcion' => now(),
                'numeroContacto' => $request->numeroContacto,
                'idGrado' => $request->idGrado,
                'idConvocatoria' => $request->idConvocatoria,
                'idArea' => $request->idArea,
                'idDelegacion' => $request->idDelegacion,
                'idCategoria' => $request->idCategoria,
            ]);

            // Relacionar con el estudiante y sus tutores
            foreach ($estudiante->tutores as $tutor) {
                $inscripcion->tutores()->attach($tutor->id, [
                    'idEstudiante' => $estudiante->id
                ]);
            }

            return redirect()->route('estudiantes.lista')
                ->with('success', 'Inscripción completada correctamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al completar la inscripción: ' . $e->getMessage())
                ->withInput();
        }
    }
}