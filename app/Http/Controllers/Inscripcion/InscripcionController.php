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
       
        $request->validate([
            'numeroContacto' => 'required|string|max:15',
            'idGrado' => 'required|integer',
            'idConvocatoria' => 'required|integer',
            'idArea' => 'required|integer',
            'idDelegacion' => 'required|integer',
        ]);

        $inscripcion= Inscripcion::create([
            'fechaInscripcion' => now(),
            'numeroContacto' => $request->numeroContacto,
            'idGrado' => $request->idGrado,
            'idConvocatoria' => $request->idConvocatoria,
            'idArea' => $request->idArea,
            'idDelegacion' => $request->idDelegacion,
        ]);

        $idTutorToken = new ObtenerIdTutorToken();
        $idTutor = $idTutorToken->obtenerIdTutorDesdeToken($request->tokenTutor);
        if ($idTutor instanceof \Illuminate\Http\JsonResponse) {
            return $idTutor; // Retorna la respuesta JSON si no se obtiene el ID del tutor
        }
        //Logica para relacionar la inscripcion con el tutor y estudiante

        $userId = Auth::id();//Dado qeu sera solo para Estudiantes

        $inscripcion->tutores()->attach($idTutor, [
            'idEstudiante' => $userId,//Recuperar el Id del Est
        ]);

        return redirect()->route('dashboard')->with('success', 'Inscripción realizada correctamente');
    
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
