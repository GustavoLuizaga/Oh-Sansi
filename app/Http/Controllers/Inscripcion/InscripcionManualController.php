<?php

namespace App\Http\Controllers\Inscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Grado;
use App\Models\GradoCategoria;
use App\Models\GrupoInscripcion;
use App\Models\Inscripcion;

class InscripcionManualController extends Controller
{
    public function index()
    {
        $areas = Area::all();
        return view('inscripciones.formInscripcionEst', compact('areas'));
    }

    public function buscarEstudiante(Request $request)
    {
        try {
            $ci = $request->ci;
            
            $estudiante = User::where('ci', $ci)
                ->whereHas('roles', function($query) {
                    $query->where('nombre', 'estudiante');
                })
                ->with('estudiante')
                ->first();

            if (!$estudiante) {
                return response()->json([
                    'success' => false,
                    'message' => 'Estudiante no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'estudiante' => [
                    'nombres' => $estudiante->name,
                    'apellidoPaterno' => $estudiante->apellidoPaterno,
                    'apellidoMaterno' => $estudiante->apellidoMaterno,
                    'ci' => $estudiante->ci,
                    'fechaNacimiento' => $estudiante->fechaNacimiento,
                    'genero' => $estudiante->genero,
                    'email' => $estudiante->email,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar estudiante',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerCategorias(Request $request, $idArea)
    {
        try {
            // Get categories that belong to this area and are in the published convocatoria
            $categorias = Categoria::whereHas('convocatoriaAreaCategorias', function($query) use ($idArea) {
                $query->where('idArea', $idArea)
                      ->whereHas('convocatoria', function($q) {
                          $q->where('estado', 'Publicada');
                      });
            })->get();

            return response()->json([
                'success' => true,
                'categorias' => $categorias
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener categorías',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerGrados(Request $request)
    {
        try {
            $categoriaIds = $request->categorias;
            
            // Get unique grades from all selected categories
            $grados = Grado::whereHas('categorias', function($query) use ($categoriaIds) {
                $query->whereIn('categoria.idCategoria', $categoriaIds);
            })->get();

            return response()->json([
                'success' => true,
                'grados' => $grados
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener grados',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerGrupos(Request $request, $modalidad)
    {
        try {
            // Obtener el tutor autenticado
            $tutor = auth()->user()->tutor;
            
            if (!$tutor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no es un tutor'
                ], 403);
            }
            
            // Obtener la delegación del tutor
            $idDelegacion = $tutor->primerIdDelegacion();
            
            if (!$idDelegacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tutor no tiene delegación asignada'
                ], 404);
            }
            
            // Obtener grupos de la misma delegación con estado activo o incompleto
            $grupos = GrupoInscripcion::where('idDelegacion', $idDelegacion)
                ->where('modalidad', $modalidad)
                ->whereIn('estado', ['activo', 'incompleto'])
                ->get();

            return response()->json([
                'success' => true,
                'grupos' => $grupos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener grupos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}