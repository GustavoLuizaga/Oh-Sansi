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
use App\Models\User;
use App\Models\Rol;
use App\Models\Area; // Add this at the top of your file
use App\Notifications\WelcomeEmailNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use App\Models\TutorEstudianteInscripcion;     
use App\Models\BoletaPago;
use App\Models\BoletaPagoInscripcion;
use App\Models\Tutor;
use App\Models\Categoria;
use App\Models\Convocatoria;
use App\Models\Grado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;

class InscripcionController extends Controller
{
    public $conv;
    public function index()
    {
        // // IGNOREN ESTO, NO AFECTA EN NADA SOLO ESTABA PROBANDO UNAS COSITAS
        // $user = Auth::user();
        // // Obtener todos los idEstudiante únicos de la tabla tutorEstudianteInscripcion
        // $estudiantesInscritos = TutorEstudianteInscripcion::select('idEstudiante')
        //     ->distinct()
        //     ->pluck('idEstudiante')
        //     ->toArray();
        // // Verificar si el usuario actual es un estudiante inscrito
        // if ($user && $user->estudiante && in_array($user->id, $estudiantesInscritos)) {
        //     return view('inscripciones.FormularioDatosInscripcionEst', [
        //         'convocatoriaActiva' => false // O true, según tu lógica
        //     ]);
        // }

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
        $conv = $idConvocatoria;

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

    public function informacionEstudiante()
    {
        // Obtener el ID de la convocatoria activa
        $convocatoria = new VerificarExistenciaConvocatoria();
        $idConvocatoriaResult = $convocatoria->verificarConvocatoriaActiva();

        // Verificar si hay una convocatoria activa
        if ($idConvocatoriaResult instanceof \Illuminate\Http\JsonResponse) {
            // No hay convocatoria activa
            return view('inscripciones.FormularioDatosInscripcionEst', [
                'convocatoriaActiva' => false
            ]);
        }

        return view('inscripciones.FormularioDatosInscripcionEst');
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
                'idCategoria' => $request->idCategoria,
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
        $user = Auth::user();
        $token = null;
        $areas = collect();

        // revisamos si el usuario es un tutor
        if ($user->tutor && $user->tutor->tutorAreaDelegacion) {
            // obtenemos las areas y el token del tutor
            $areas = Area::join('tutorAreaDelegacion', 'area.idArea', '=', 'tutorAreaDelegacion.idArea')
                        ->where('tutorAreaDelegacion.id', $user->tutor->id)
                        ->select('area.*')
                        ->get();
            $token = $user->tutor->tutorAreaDelegacion->tokenTutor;
        } else {
            // mostramos las area en la convocatoria activa
            $convocatoria = \App\Models\Convocatoria::where('estado', 'Publicada')->first();
            if ($convocatoria) {
                $areas = Area::join('convocatoriaAreaCategoria', 'area.idArea', '=', 'convocatoriaAreaCategoria.idArea')
                            ->where('convocatoriaAreaCategoria.idConvocatoria', $convocatoria->idConvocatoria)
                            ->select('area.*')
                            ->distinct()
                            ->get();
            }
        }

        // obtenemos el id de la convocatoria activa para mostrar en el select de la inscripcio
        $convocatoria = \App\Models\Convocatoria::where('estado', 'Publicada')
                        ->first();
        
        $idConvocatoriaResult = $convocatoria ? $convocatoria->idConvocatoria : null;
        
        return view('inscripciones.inscripcionTutor', compact('areas', 'token', 'idConvocatoriaResult'));
    }

    public function storeManual(Request $request)
    {
        try {
            // Validar los datos del formulario
            $validated = $request->validate([
                'nombres' => 'required|string|max:255',
                'apellidoPaterno' => 'required|string|max:255',
                'apellidoMaterno' => 'required|string|max:255',
                'ci' => 'required|string|max:50',
                'fechaNacimiento' => 'required|date',
                'nombreCompletoTutor' => 'required|string|max:255',
                'correoTutor' => 'required|email|max:255',
                'email' => 'required|email|max:255',
                'telefono' => 'required|string|max:50',
                'area' => 'required|integer',
                'categoria' => 'required|integer',
                'grado' => 'required|integer',
            ]);

            //Verificamos si el estudiante ya tiene cuenta para crearla o no
            $user = User::where('email', $request->email)->first();
            $isNewUser = false;
            if (!$user) {
                $plainPassword = $request->ci;
                $user = User::create([
                    'name' => $request->nombres,
                    'apellidoPaterno' => $request->apellidoPaterno,
                    'apellidoMaterno' => $request->apellidoMaterno,
                    'ci' => $request->ci,
                    'email' => $request->email,
                    'fechaNacimiento' => $request->fechaNacimiento,
                    'genero' => $request->genero,
                    'password' => Hash::make($request->ci)
                ]);

                $rol = Rol::find(3);
                if ($rol) {
                    $user->roles()->attach($rol->idRol, ['habilitado' => true]);
                    $user->estudiante()->create();
                }

                $isNewUser = true;
            }

            if ($isNewUser) {
                $user->notify(new WelcomeEmailNotification($plainPassword));
                event(new Registered($user));
            }

            $areasInscritas = TutorEstudianteInscripcion::where('idEstudiante', $user->id)
                ->with('inscripcion.area')
                ->get()
                ->pluck('inscripcion.area.nombre')
                ->filter()
                ->unique()
                ->values();

                if ($areasInscritas->contains($request->area)) {
                    return back()->with('error', "El estudiante ya está inscrito en el área '{$request->area}'.");
                }

            $convocatoria = new VerificarExistenciaConvocatoria();
            $idConvocatoriaResult = $convocatoria->verificarConvocatoriaActiva();

            $delegado = Auth::user();
            $idDelegacion = $delegado->tutor->primerIdDelegacion();

            // Crear la inscripción
            $inscripcion = Inscripcion::create([
                'fechaInscripcion' => now(),
                'numeroContacto' => $request->telefono,
                'idGrado' => $request->grado,
                'idConvocatoria' => $idConvocatoriaResult,
                'idArea' => $request->area,
                'idDelegacion' => $idDelegacion,
                'idCategoria' => $request->categoria,
                'nombreApellidosTutor' => $request->nombreCompletoTutor,
                'correoTutor' => $request->correoTutor,
            ]);

            // Relacionar con tutores
            $inscripcion->tutores()->attach(Auth::user()->id, [
                'idEstudiante' => $user->id,
            ]);

            return redirect()->route('dashboard')->with('success', 'Inscripción realizada correctamente');
        } catch (\Exception $e) {
            Log::error('Error en inscripción:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Hubo un error al procesar la inscripción. Por favor, intente nuevamente.');
        }
    }

    public function ImprimirFormularioInscripcion()
    {
        $estudianteId = Auth::id();
    
        // Obtener datos del estudiante y sus inscripciones con todos los campos solicitados
        $data = DB::table('tutorestudianteinscripcion')
            ->select([
                // IDs principales
                'estudiante.id AS estudiante_id',
                'tutor.id AS tutor_id',
                'inscripcion.idInscripcion AS inscripcion_id',
                'convocatoria.idConvocatoria AS convocatoria_id',
                'area.idArea AS area_id',
                'categoria.idCategoria AS categoria_id',
                'delegacion.idDelegacion AS delegacion_id',
                'grado.idGrado AS grado_id',
                
                // Datos del estudiante
                'estudiante.name AS estudiante_nombre',
                'estudiante.apellidoPaterno AS estudiante_apellido_paterno',
                'estudiante.apellidoMaterno AS estudiante_apellido_materno',
                'estudiante.ci AS estudiante_ci',
                'grado.grado AS estudiante_grado',
                'estudiante.fechaNacimiento AS estudiante_nacimiento',
                'estudiante.genero AS estudiante_genero',
                
                // Datos del tutor
                'tutor_user.name AS tutor_nombre',
                'tutor_user.apellidoPaterno AS tutor_apellido_paterno',
                'tutor_user.apellidoMaterno AS tutor_apellido_materno',
                'tutor_user.ci AS tutor_ci',
                'tutor.profesion AS tutor_profesion',
                'tutor.telefono AS tutor_telefono',
                'tutor_user.email AS tutor_email',
                'delegacion.nombre AS tutor_colegio',
                'delegacion.dependencia AS colegio_dependencia',
                'delegacion.departamento AS colegio_departamento',
                'delegacion.provincia AS colegio_provincia',
                'delegacion.direccion AS colegio_direccion',
                'delegacion.telefono AS colegio_telefono',
                
                // Datos de área/categoría
                'area.nombre AS area_nombre',
                'categoria.nombre AS categoria_nombre',
                'detalle_inscripcion.created_at AS area_fecha_registro',
                'detalle_inscripcion.idDetalleInscripcion AS detalle_inscripcion_id',
                
                // Datos de convocatoria
                'convocatoria.nombre AS convocatoria_nombre',
                'convocatoria.fechaFin AS convocatoria_fecha_limite',
                'convocatoria.metodoPago AS convocatoria_metodo_pago',
                'convocatoria.contacto AS convocatoria_contacto',
                
                // Datos de inscripción
                'inscripcion.fechaInscripcion AS inscripcion_fecha',
                'inscripcion.numeroContacto AS inscripcion_numero_contacto',
                'inscripcion.status AS inscripcion_status',
                
                // Precio (usamos precioIndividual como valor estático)
                DB::raw('15 AS precio'), // Valor estático de 15 Bs para INDIVIDUAL
                DB::raw("'INDIVIDUAL' AS modalidad") // Modalidad estática
            ])
            ->join('inscripcion', 'tutorestudianteinscripcion.idInscripcion', '=', 'inscripcion.idInscripcion')
            ->join('users AS estudiante', 'tutorestudianteinscripcion.idEstudiante', '=', 'estudiante.id')
            ->join('tutor', 'tutorestudianteinscripcion.idTutor', '=', 'tutor.id')
            ->join('users AS tutor_user', 'tutor.id', '=', 'tutor_user.id')
            ->join('detalle_inscripcion', 'inscripcion.idInscripcion', '=', 'detalle_inscripcion.idInscripcion')
            ->join('area', 'detalle_inscripcion.idArea', '=', 'area.idArea')
            ->join('tutorareadelegacion', function($join) {
                $join->on('tutor.id', '=', 'tutorareadelegacion.id')
                    ->on('area.idArea', '=', 'tutorareadelegacion.idArea');
            })
            ->join('delegacion', 'tutorareadelegacion.idDelegacion', '=', 'delegacion.idDelegacion')
            ->join('categoria', 'detalle_inscripcion.idCategoria', '=', 'categoria.idCategoria')
            ->join('grado', 'inscripcion.idGrado', '=', 'grado.idGrado')
            ->join('convocatoria', 'inscripcion.idConvocatoria', '=', 'convocatoria.idConvocatoria')
            ->where('tutorestudianteinscripcion.idEstudiante', $estudianteId)
            ->where('convocatoria.estado', 'Publicada') // Solo convocatorias publicadas
            ->get();
    
        if ($data->isEmpty()) {
            return back()->with('error', 'No se encontraron inscripciones para mostrar la boleta');
        }
        
        // Obtener información de la boleta de pago
        $boletaInfo = $this->obtenerDatosBoleta($estudianteId);
        
        // Procesar los datos para la vista
        $processed = [
            'codigoOrden' => $boletaInfo['codigoBoleta'],
            'fechaGeneracion' => $boletaInfo['fechaInicio'],
            'fechaVencimiento' => $boletaInfo['fechaFin'],
            
            'ids' => [
                'estudiante_id' => $data->first()->estudiante_id,
                'tutor_id' => $data->first()->tutor_id,
                'inscripcion_id' => $data->first()->inscripcion_id,
                'convocatoria_id' => $data->first()->convocatoria_id,
                'delegacion_id' => $data->first()->delegacion_id,
                'grado_id' => $data->first()->grado_id
            ],
            
            'estudiante' => [
                'id' => $data->first()->estudiante_id,
                'nombre' => $data->first()->estudiante_nombre,
                'apellido_paterno' => $data->first()->estudiante_apellido_paterno,
                'apellido_materno' => $data->first()->estudiante_apellido_materno,
                'ci' => $data->first()->estudiante_ci,
                'grado' => $data->first()->estudiante_grado,
                'fecha_nacimiento' => $data->first()->estudiante_nacimiento,
                'genero' => $data->first()->estudiante_genero
            ],
            
            'tutores' => $data->groupBy('tutor_ci')->map(function($tutorGroup) {
                $first = $tutorGroup->first();
                return [
                    'id' => $first->tutor_id,
                    'nombre' => $first->tutor_nombre,
                    'apellido_paterno' => $first->tutor_apellido_paterno,
                    'apellido_materno' => $first->tutor_apellido_materno,
                    'ci' => $first->tutor_ci,
                    'profesion' => $first->tutor_profesion,
                    'telefono' => $first->tutor_telefono,
                    'email' => $first->tutor_email,
                    'areas' => $tutorGroup->map(function($item) {
                        return [
                            'id' => $item->area_id,
                            'nombre' => $item->area_nombre,
                            'categoria_id' => $item->categoria_id,
                            'categoria' => $item->categoria_nombre,
                            'detalle_inscripcion_id' => $item->detalle_inscripcion_id,
                            'fecha_registro' => $item->area_fecha_registro
                        ];
                    })->toArray(),
                    'colegio' => [
                        'id' => $first->delegacion_id,
                        'nombre' => $first->tutor_colegio,
                        'dependencia' => $first->colegio_dependencia,
                        'departamento' => $first->colegio_departamento,
                        'provincia' => $first->colegio_provincia,
                        'direccion' => $first->colegio_direccion,
                        'telefono' => $first->colegio_telefono
                    ]
                ];
            })->values()->toArray(),
            
            'convocatoria' => [
                'id' => $data->first()->convocatoria_id,
                'nombre' => $data->first()->convocatoria_nombre,
                'fecha_limite' => $data->first()->convocatoria_fecha_limite,
                'metodo_pago' => $data->first()->convocatoria_metodo_pago,
                'contacto' => $data->first()->convocatoria_contacto
            ],
            
            'inscripcion' => [
                'id' => $data->first()->inscripcion_id,
                'fecha' => $data->first()->inscripcion_fecha,
                'numero_contacto' => $data->first()->inscripcion_numero_contacto,
                'status' => $data->first()->inscripcion_status,
                'grado_id' => $data->first()->grado_id
            ],
            
            'inscripciones' => $data->map(function($item) {
                return [
                    'modalidad' => $item->modalidad,
                    'area_id' => $item->area_id,
                    'area' => $item->area_nombre,
                    'categoria_id' => $item->categoria_id,
                    'categoria' => $item->categoria_nombre,
                    'detalle_inscripcion_id' => $item->detalle_inscripcion_id,
                    'fecha_registro' => $item->area_fecha_registro,
                    'precio' => (float)$item->precio
                ];
            })->toArray(),
        ];
    
        // Calcular el total automáticamente
        $processed['totalPagar'] = array_sum(array_column($processed['inscripciones'], 'precio'));
        
        return view('inscripciones.ImprimirFormularioInscripcionEst', $processed);
    }
    
    /**
     * Obtiene datos de boleta de pago existente para las inscripciones del estudiante
     * Si no existe una boleta, devuelve valores por defecto
     * 
     * @param int $estudianteId
     * @return array
     */
    private function obtenerDatosBoleta($estudianteId)
    {
        try {
            // Obtener IDs de inscripciones del estudiante
            $inscripcionesIds = DB::table('tutorestudianteinscripcion')
                ->where('idEstudiante', $estudianteId)
                ->pluck('idInscripcion');
            
            // Buscar si existe una boleta para alguna de estas inscripciones
            $boletaInfo = DB::table('boletapagoinscripcion')
                ->join('boletapago', 'boletapagoinscripcion.idBoleta', '=', 'boletapago.idBoleta')
                ->whereIn('boletapagoinscripcion.idInscripcion', $inscripcionesIds)
                ->select(
                    'boletapago.CodigoBoleta',
                    'boletapago.fechainicio',
                    'boletapago.fechafin'
                )
                ->first();
            
            if ($boletaInfo) {
                // Si existe boleta, devolver sus datos
                return [
                    'codigoBoleta' => $boletaInfo->CodigoBoleta,
                    'fechaInicio' => $boletaInfo->fechainicio,
                    'fechaFin' => $boletaInfo->fechafin
                ];
            }
            
            // Si no existe boleta, devolver valores por defecto
            return [
                'codigoBoleta' => null, // Esto se mostrará como "GENERA BOLETA DE PAGO"
                'fechaInicio' => now()->format('d/m/Y H:i'),
                'fechaFin' => null
            ];
        } 
        catch (\Exception $e) {
            Log::error('Error obteniendo datos de boleta para estudiante:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // En caso de error, devolver valores por defecto
            return [
                'codigoBoleta' => null,
                'fechaInicio' => now()->format('d/m/Y H:i'),
                'fechaFin' => null
            ];
        }
    }
}
