<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\TutorAreaDelegacion;
use App\Models\Inscripcion;
use App\Http\Controllers\Inscripcion\ObtenerAreasConvocatoria;
use App\Http\Controllers\Inscripcion\VerificarExistenciaConvocatoria;
use App\Http\Controllers\Inscripcion\ObtenerCategoriasArea;
use App\Http\Controllers\Inscripcion\ObtenerGradosArea;
use App\Http\Controllers\Inscripcion\ObtenerIdTutorToken;

class InscripcionController extends Controller
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
                'convocatoriaActiva' => false
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
            // Validar los datos del formulario
            $request->validate([
                'numeroContacto' => 'required|string|max:8',
                'tutor_tokens' => 'required|array|min:1',
                'tutor_areas' => 'required|array|min:1',
                'tutor_delegaciones' => 'required|array|min:1',
                'idCategoria' => 'required|integer',
                'idGrado' => 'required|integer',
                'idConvocatoria' => 'required|integer'
            ]);

            // Crear la inscripción
            $inscripcion = Inscripcion::create([
                'fechaInscripcion' => now(),
                'numeroContacto' => $request->numeroContacto,
                'idGrado' => $request->idGrado,
                'idConvocatoria' => $request->idConvocatoria,
                'idArea' => $request->tutor_areas[0], // Usar el área del primer tutor
                'idDelegacion' => $request->tutor_delegaciones[0], // Usar la delegación del primer tutor
                'idCategoria' => $request->idCategoria
            ]);

            // Relacionar con tutores
            foreach ($request->tutor_tokens as $index => $token) {
                $tutorAreaDelegacion = TutorAreaDelegacion::where('tokenTutor', $token)->first();
                
                if ($tutorAreaDelegacion) {
                    // Relacionar la inscripción con el tutor y el estudiante
                    $inscripcion->tutores()->attach($tutorAreaDelegacion->id, [
                        'idEstudiante' => Auth::id()
                    ]);
                }
            }

            return redirect()->route('dashboard')->with('success', 'Inscripción realizada correctamente');

        } catch (\Exception $e) {
            Log::error('Error en inscripción:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Hubo un error al procesar la inscripción. Por favor, intente nuevamente.');
        }
    }

    public function showTutorProfile()
    {
        try {
            $user = Auth::user();
            $token = null;

            if ($user && $user->tutor) {
                Log::info('User and tutor found:', ['user_id' => $user->id, 'tutor_id' => $user->tutor->id]);
                
                $tutorAreaDelegacion = TutorAreaDelegacion::where('id', $user->tutor->id)
                    ->select('tokenTutor')
                    ->first();

                if ($tutorAreaDelegacion) {
                    Log::info('Token found:', ['token' => $tutorAreaDelegacion->tokenTutor]);
                    $token = $tutorAreaDelegacion->tokenTutor;
                } else {
                    Log::warning('No token found for tutor:', ['tutor_id' => $user->tutor->id]);
                }
            } else {
                Log::warning('No tutor found for user:', ['user_id' => $user->id ?? 'null']);
            }

            return view('inscripciones.inscripcionTutor', compact('token'));
        } catch (\Exception $e) {
            Log::error('Error in showTutorProfile:', ['error' => $e->getMessage()]);
            return view('inscripciones.inscripcionTutor', ['token' => null]);
        }
    }
}
