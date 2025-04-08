<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Inscripcion\ObtenerAreasConvocatoria;
use App\Http\Controllers\Inscripcion\VerificarExistenciaConvocatoria;
use App\Http\Controllers\Inscripcion\ObtenerCategoriasArea;
use App\Http\Controllers\Inscripcion\ObtenerGradosArea;
use App\Models\Inscripcion;
use App\Http\Controllers\Inscripcion\ObtenerIdTutorToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $request->validate([
            'numeroContacto' => 'required|string|max:15',
            'tutor_tokens' => 'required|array|min:1',
            'tutor_areas' => 'required|array|min:1',
            'tutor_delegaciones' => 'required|array|min:1',
            'idGrado' => 'required|integer',
            'idConvocatoria' => 'required|integer',
            'idCategoria' => 'required|integer',
        ]);

        // Verificar que los tokens sean válidos
        $validTokens = [];
        $idTutorToken = new ObtenerIdTutorToken();
        
        foreach ($request->tutor_tokens as $index => $token) {
            $tutor = \App\Models\TutorAreaDelegacion::where('tokenTutor', $token)->first();
            if ($tutor) {
                $validTokens[] = [
                    'token' => $token,
                    'idTutor' => $tutor->id,
                    'idArea' => $tutor->idArea,
                    'idDelegacion' => $tutor->idDelegacion
                ];
            }
        }
        
        // Verificar que haya al menos un token válido
        if (empty($validTokens)) {
            return back()->withErrors(['tutor_tokens' => 'Debe proporcionar al menos un token de tutor válido.']);
        }

        $inscripcion = Inscripcion::create([
            'fechaInscripcion' => now(),
            'numeroContacto' => $request->numeroContacto,
            'idGrado' => $request->idGrado,
            'idConvocatoria' => $request->idConvocatoria,
            'idArea' => $validTokens[0]['idArea'], // Usamos el área del primer tutor válido
            'idDelegacion' => $validTokens[0]['idDelegacion'], // Usamos la delegación del primer tutor válido
            'idCategoria' => $request->idCategoria,
        ]);

        $userId = Auth::id(); // ID del estudiante autenticado
        
        // Relacionar la inscripción con los tutores válidos
        foreach ($validTokens as $tutorData) {
            $inscripcion->tutores()->attach($tutorData['idTutor'], [
                'idEstudiante' => $userId,
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Inscripción completada con éxito');
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

}
