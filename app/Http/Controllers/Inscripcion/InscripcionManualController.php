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
use App\Models\Convocatoria;
use App\Models\TutorEstudianteInscripcion;
use App\Models\Delegacion;
use App\Models\DetalleInscripcion;
use Illuminate\Support\Facades\Log;
class InscripcionManualController extends Controller
{
    public function index()
    {
        try {
            // Get active convocatoria with estado='Publicada'
            $convocatoria = Convocatoria::where('estado', 'Publicada')->first();
            
            // Get authenticated tutor and their delegation
            $tutor = auth()->user()->tutor;
            $delegacion = null;
            $nombreColegio = null;
            
            if ($tutor) {
                // Get the first delegation associated with this tutor
                $idDelegacion = $tutor->primerIdDelegacion();
                if ($idDelegacion) {
                    $delegacion = Delegacion::find($idDelegacion);
                    if ($delegacion) {
                        $nombreColegio = $delegacion->nombre;
                    }
                }
            }

            // Debug logging
            Log::info('Convocatoria:', ['convocatoria' => $convocatoria ? $convocatoria->toArray() : 'No hay convocatoria publicada']);
            Log::info('Delegacion:', ['delegacion' => $delegacion ? $delegacion->toArray() : 'No hay delegación asignada']);
            Log::info('Colegio:', ['nombreColegio' => $nombreColegio ?? 'No hay colegio asignado']);

            $areas = Area::all();
            
            return view('inscripciones.formInscripcionEst', compact('areas', 'convocatoria', 'delegacion', 'nombreColegio'));
            
        } catch (\Exception $e) {
            Log::error('Error in InscripcionManualController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el formulario');
        }
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
            $selectedCategoriaId = $request->selectedCategoria;

            // Get active convocatoria
            $convocatoria = Convocatoria::where('estado', 'Publicada')->first();
            if (!$convocatoria) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay convocatoria activa'
                ], 404);
            }

            $query = Categoria::whereHas('convocatoriaAreaCategorias', function($query) use ($idArea, $convocatoria) {
                $query->where('idArea', $idArea)
                      ->where('idConvocatoria', $convocatoria->idConvocatoria);
            });

            // If there's a selected categoria from another area, filter by common grades
            if ($selectedCategoriaId) {
                $query->whereHas('grados', function($gradosQuery) use ($selectedCategoriaId) {
                    $gradosQuery->whereIn('grado.idGrado', function($subQuery) use ($selectedCategoriaId) {
                        $subQuery->select('gradocategoria.idGrado')
                                ->from('gradocategoria')
                                ->where('gradocategoria.idCategoria', $selectedCategoriaId);
                    });
                });
            }

            $categorias = $query->get();

            return response()->json([
                'success' => true,
                'categorias' => $categorias,
                'convocatoriaId' => $convocatoria->idConvocatoria
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
            
            // If we have multiple categories, get only grades that are common to all categories
            if (count($categoriaIds) > 1) {
                $grados = Grado::whereHas('categorias', function($query) use ($categoriaIds) {
                    $query->whereIn('categoria.idCategoria', [$categoriaIds[0]]);
                })->whereHas('categorias', function($query) use ($categoriaIds) {
                    $query->whereIn('categoria.idCategoria', [$categoriaIds[1]]);
                })->get();
            } else {
                // For single category, get all its grades
                $grados = Grado::whereHas('categorias', function($query) use ($categoriaIds) {
                    $query->whereIn('categoria.idCategoria', $categoriaIds);
                })->get();
            }

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
                'grupos' => $grupos,
                'delegacionId' => $idDelegacion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener grupos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('Starting inscription process', ['request' => $request->all()]);

            // Get authenticated user and verify tutor role
            $user = auth()->user();
            $tutor = $user->tutor;

            if (!$tutor) {
                Log::error('Tutor not found for user', ['userId' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del tutor'
                ], 404);
            }

            // 1. Find existing student by CI and get their ID
            $estudiante = User::where('ci', $request->ci)
                ->whereHas('roles', function($query) {
                    $query->where('nombre', 'estudiante');
                })
                ->first();

            if (!$estudiante) {
                return response()->json([
                    'success' => false,
                    'message' => 'Estudiante no encontrado'
                ], 404);
            }

            // Get the estudiante record specifically
            $estudianteRecord = $estudiante->estudiante;
            
            if (!$estudianteRecord) {
                Log::error('Student record not found for user', [
                    'userId' => $estudiante->id,
                    'ci' => $estudiante->ci
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Registro de estudiante no encontrado'
                ], 404);
            }

            // Check if student already has an inscription for this convocatoria
            $existingInscription = Inscripcion::whereHas('estudiantes', function($query) use ($estudianteRecord) {
                $query->where('estudiante.id', $estudianteRecord->id);
            })->where('idConvocatoria', $request->idConvocatoria)->first();

            if ($existingInscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'El estudiante ya tiene una inscripción en esta convocatoria'
                ], 422);
            }

            // Continue with inscription creation...
            // Rest of the store method remains the same...
            $inscripcion = Inscripcion::create([
                'fechaInscripcion' => now(),
                'numeroContacto' => $request->numeroContacto,
                'status' => 'pendiente',
                'idGrado' => $request->grado,
                'idConvocatoria' => $request->idConvocatoria,
                'idDelegacion' => $request->idDelegacion,
                'nombreApellidosTutor' => $request->nombreCompletoTutor,
                'correoTutor' => $request->correoTutor,
            ]);

            // 3. Create inscription details for each area
            foreach ($request->areas as $area) {
                DetalleInscripcion::create([
                    'idInscripcion' => $inscripcion->idInscripcion,
                    'idArea' => $area['area'],
                    'idCategoria' => $area['categoria'],
                    'modalidadInscripcion' => $area['modalidad'],
                    'idGrupoInscripcion' => isset($area['grupo']) ? $area['grupo'] : null
                ]);
            }

            // 4. Create tutor-student-inscription relationship using the correct IDs
            TutorEstudianteInscripcion::create([
                'idTutor' => $tutor->id,
                'idEstudiante' => $estudianteRecord->id, // Using the correct student ID
                'idInscripcion' => $inscripcion->idInscripcion
            ]);

            Log::info('Inscription completed successfully', [
                'inscripcionId' => $inscripcion->idInscripcion,
                'estudiante' => $estudiante->ci,
                'estudianteId' => $estudianteRecord->id,
                'tutorId' => $tutor->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Inscripción realizada con éxito',
                'redirect' => route('inscripcion.estudiante.informacion')
            ]);

        } catch (\Exception $e) {
            Log::error('Error in inscription process: ' . $e->getMessage(), [
                'user' => auth()->user()->id ?? 'no user',
                'tutor' => auth()->user()->tutor->id ?? 'no tutor'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la inscripción: ' . $e->getMessage()
            ], 500);
        }
    }

    public function obtenerConvocatoriaActiva()
        {
            try {
                Log::info('Buscando convocatoria activa...');
                
                $convocatoria = Convocatoria::where('estado', 'Publicada')
                    ->orderBy('fechaInicio', 'desc')
                    ->first();
                
                Log::info('Resultado búsqueda convocatoria:', [
                    'encontrada' => $convocatoria ? 'sí' : 'no',
                    'datos' => $convocatoria ? $convocatoria->toArray() : null
                ]);
    
                if (!$convocatoria) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No hay convocatoria activa disponible'
                    ], 404);
                }
    
                return response()->json([
                    'success' => true,
                    'convocatoria' => [
                        'id' => $convocatoria->idConvocatoria,
                        'nombre' => $convocatoria->nombre,
                        'estado' => $convocatoria->estado
                    ]
                ]);
            } catch (\Exception $e) {
                Log::error('Error al obtener convocatoria: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener la convocatoria',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

    public function obtenerColegio()
        {
            try {
                $tutor = auth()->user()->tutor;
                
                if (!$tutor) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Usuario no es un tutor'
                    ], 403);
                }
                
                $idDelegacion = $tutor->primerIdDelegacion();
                if (!$idDelegacion) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tutor no tiene delegación asignada'
                    ], 404);
                }
    
                $delegacion = Delegacion::find($idDelegacion);
                if (!$delegacion) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Delegación no encontrada'
                    ], 404);
                }
    
                return response()->json([
                    'success' => true,
                    'colegio' => [
                        'id' => $delegacion->idDelegacion,
                        'nombre' => $delegacion->nombre
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener el colegio',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
}