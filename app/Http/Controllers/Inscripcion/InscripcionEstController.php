<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TutorAreaDelegacion;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Inscripcion\ObtenerAreasConvocatoria;
use App\Http\Controllers\Inscripcion\VerificarExistenciaConvocatoria;
use App\Http\Controllers\Inscripcion\ObtenerCategoriasArea;
use App\Http\Controllers\Inscripcion\ObtenerGradosArea;
use App\Http\Controllers\Inscripcion\ObtenerIdTutorToken;

class InscripcionEstController extends Controller
{
    public function index()
    {
        // Obtener el ID de la convocatoria activa
        $convocatoria = new VerificarExistenciaConvocatoria();
        $idConvocatoriaResult = $convocatoria->verificarConvocatoriaActiva();
        
        // Verificar si hay una convocatoria activa
        if ($idConvocatoriaResult instanceof \Illuminate\Http\JsonResponse) {
            // No hay convocatoria activa
            return view('inscripciones.inscripcionEstudiante', [
                'convocatoriaActiva' => false,
                'convocatoria' => null
            ]);
        }
        
        $idConvocatoria = $idConvocatoriaResult;
        
        // Obtener la información de la convocatoria
        $convocatoriaInfo = \App\Models\Convocatoria::find($idConvocatoria);
        
        // Obtener las delegaciones (colegios)
        $colegios = \App\Models\Delegacion::select('idDelegacion as id', 'nombre')
                        ->orderBy('nombre')
                        ->get();

        // Obtener las areas por el id de la convocatoria
        $obtenerAreas = new ObtenerAreasConvocatoria();
        $areas = $obtenerAreas->obtenerAreasPorConvocatoria($idConvocatoria);

        // Obtener las categorias por el id de la convocatoria
        $obtenerCategorias = new ObtenerCategoriasArea();
        $categorias = $obtenerCategorias->categoriasAreas($idConvocatoria);

        // Obtener los grados por las categorias
        $obtenerGrados = new ObtenerGradosArea();
        $grados = $obtenerGrados->obtenerGradosPorArea($categorias);

        return view('inscripciones.inscripcionEstudiante', [
            'convocatoriaActiva' => true,
            'convocatoria' => $convocatoriaInfo,
            'areas' => $areas,
            'categorias' => $categorias,
            'grados' => $grados,
            'colegios' => $colegios
        ]);
    }

    public function store(Request $request)
    {
        try {
            // Validar solo los campos necesarios
            $validatedData = $request->validate([
                'numeroContacto' => 'required|string|size:8',
                'tutor_tokens' => 'required|array|min:1',
                'tutor_areas' => 'required|array|min:1',
                'tutor_delegaciones' => 'required|array|min:1',
                'idConvocatoria' => 'required|integer'
                
            ]);

            // Verificar los tokens de tutor
            $validTokens = [];
            foreach ($request->tutor_tokens as $index => $token) {
                $tutorAreaDelegacion = TutorAreaDelegacion::where('tokenTutor', $token)->first();
                
                if (!$tutorAreaDelegacion) {
                    return back()->withErrors(['error' => 'Token de tutor inválido'])->withInput();
                }
                
                $validTokens[] = $tutorAreaDelegacion;
            }

            // Crear inscripciones para cada área seleccionada
            $inscripciones = [];
            
            foreach ($request->tutor_areas as $index => $idArea) {
                // Verificar que el índice existe en los arrays
                if (!isset($request->tutor_delegaciones[$index])) {
                    continue;
                }
                
                // Crear la inscripción para esta área
                $inscripcion = Inscripcion::create([
                    'fechaInscripcion' => now(),
                    'numeroContacto' => $request->numeroContacto,
                    'idConvocatoria' => $request->idConvocatoria,
                    'idArea' => $idArea,
                    'idDelegacion' => $request->tutor_delegaciones[$index],
                    'idCategoria' => $request->idCategoria, 
                    'idGrado' => $request->idGrado ?? 1 // valor por defecto si no se proporciona
                ]);
                
                $inscripciones[] = $inscripcion;
                
                // Relacionar con tutores
                foreach ($validTokens as $tutorAreaDelegacion) {
                    $inscripcion->tutores()->attach($tutorAreaDelegacion->id, [
                        'idEstudiante' => Auth::id()
                    ]);
                }
            }

            return redirect()->route('dashboard')->with('success', 'Inscripción realizada correctamente');

        } catch (\Exception $e) {
            Log::error('Error en inscripción:', ['error' => $e->getMessage()]);
            return back()
                ->withErrors(['error' => 'Hubo un error al procesar la inscripción. Por favor, intente nuevamente.'])
                ->withInput();
        }
    }
    
    public function validateTutorToken($token)
    {
        try {
            Log::info('Validating token: ' . $token); // Add logging
            
            $tutor = \App\Models\TutorAreaDelegacion::where('tokenTutor', $token)->first();
            
            Log::info('Query result:', ['tutor' => $tutor]); // Add logging
            
            if (!$tutor) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Token no encontrado'
                ]);
            }
        
            // Get area and delegacion info
            $area = \App\Models\Area::find($tutor->idArea);
            $delegacion = \App\Models\Delegacion::find($tutor->idDelegacion);
            
            if (!$area || !$delegacion) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Información de área o delegación no encontrada'
                ]);
            }
        
            // Get available categories for this area
            $categorias = \App\Models\Categoria::whereHas('convocatoriaAreaCategorias', function($query) use ($tutor) {
                $query->where('idArea', $tutor->idArea);
            })->get(['idCategoria as id', 'nombre']);
            
            return response()->json([
                'valid' => true,
                'area' => $area->nombre,
                'delegacion' => $delegacion->nombre,
                'idArea' => $tutor->idArea,
                'idDelegacion' => $tutor->idDelegacion,
                'categorias' => $categorias
            ]);
        } catch (\Exception $e) {
            Log::error('Error validating tutor token: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'valid' => false,
                'message' => 'Error al validar el token: ' . $e->getMessage()
            ]);
        }
    }
    
    public function getGradosByCategoria($id)
    {
        try {
            // Obtener los grados asociados a la categoría
            $grados = \App\Models\Grado::whereHas('categorias', function($query) use ($id) {
                $query->where('categoria.idCategoria', $id);
            })->get(['idGrado as id', 'grado as nombre']);
            
            return response()->json($grados);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los grados'], 500);
        }
    }

    public function getCategoriasByAreaConvocatoria($idConvocatoria, $idArea)
    {
        try {
            // Obtener las categorías asociadas a esta área en esta convocatoria
            $categorias = \App\Models\ConvocatoriaAreaCategoria::with('categoria')
                ->where('idConvocatoria', $idConvocatoria)
                ->where('idArea', $idArea)
                ->get()
                ->pluck('categoria')
                ->unique('idCategoria')
                ->values();

            // Transformar los datos para que sean compatibles con el formato esperado por el frontend
            $categoriasFormateadas = $categorias->map(function($categoria) {
                return [
                    'idCategoria' => $categoria->idCategoria,
                    'nombre' => $categoria->nombre
                ];
            });

            return response()->json($categoriasFormateadas);
        } catch (\Exception $e) {
            Log::error('Error al obtener categorías: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener categorías'], 500);
        }
    }
    
    /**
     * Obtiene las áreas asociadas a un tutor específico según su token
     *
     * @param string $token Token del tutor
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAreasByTutorToken($token)
    {
        try {
            Log::info('Obteniendo áreas para el token: ' . $token);
            
            // Buscar todas las entradas del tutor con ese token
            $tutorAreas = \App\Models\TutorAreaDelegacion::where('tokenTutor', $token)
                ->with('area')
                ->get();
            
            if ($tutorAreas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron áreas asociadas a este token'
                ]);
            }
            
            // Extraer las áreas y formatearlas para la respuesta
            $areas = $tutorAreas->map(function($tutorArea) {
                return [
                    'idArea' => $tutorArea->idArea,
                    'nombre' => $tutorArea->area->nombre,
                    'idDelegacion' => $tutorArea->idDelegacion,
                    'delegacion' => $tutorArea->delegacion->nombre
                ];
            });
            
            return response()->json([
                'success' => true,
                'areas' => $areas
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener áreas del tutor: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener áreas: ' . $e->getMessage()
            ], 500);
        }
    }

}