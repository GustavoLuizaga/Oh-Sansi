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

class InscripcionController extends Controller
{
    public function index()
    {
        //Obtener el ID de la convocatoria activa
        $convocatoria = new VerificarExistenciaConvocatoria();
        $idConvocatoria = $convocatoria->verificarConvocatoriaActiva();//Esto solo un ID
        if ($idConvocatoria instanceof \Illuminate\Http\JsonResponse) {
            return $idConvocatoria; // Retorna la respuesta JSON si no hay convocatoria activa
        }
        //Obtener las areas por el id de la convocatoria
        $obtenerAreas = new ObtenerAreasConvocatoria();
        $areas = $obtenerAreas->obtenerAreasPorConvocatoria($idConvocatoria);//Una lista de areas
        if ($areas instanceof \Illuminate\Http\JsonResponse) {
            return $areas; // Retorna la respuesta JSON si no se obtienen áreas
        }
        //Obtener las categorias por el id de la convocatoria
        $obtenerCategorias = new ObtenerCategoriasArea();
        $categorias = $obtenerCategorias->categoriasAreas($idConvocatoria);//Una lista de categorias
        if ($categorias instanceof \Illuminate\Http\JsonResponse) {
            return $categorias; // Retorna la respuesta JSON si no se obtienen categorías
        }
        //Obtener los grados por las categorias
        $obtenerGrados = new ObtenerGradosArea();
        $grados = $obtenerGrados->obtenerGradosPorArea($categorias);//Una lista de grados
        if ($grados instanceof \Illuminate\Http\JsonResponse) {
            return $grados; // Retorna la respuesta JSON si no se obtienen grados
        }


        return view('inscripciones.inscripcionEstudiante',compact('areas','categorias','grados','idConvocatoria'));
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


        
    }
}
