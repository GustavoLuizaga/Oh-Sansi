<?php

namespace App\Http\Controllers\BoletaPago;

use App\Http\Controllers\Controller;
use App\Models\BoletaPago;
use Illuminate\Http\Request;
use App\Models\TutorEstudianteInscripcion;
use Illuminate\Support\Facades\Auth;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Log;
use App\Models\BoletaPagoInscripcion;
use App\Models\Tutor;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Convocatoria;
use App\Models\Grado;
use App\Models\VerificacionInscripcion;
use App\Models\ConvocatoriaAreaCategoria;
use App\Http\Controllers\Inscripcion\ObtenerGradosArea;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;


class BoletaDePagoDeEstudiante extends Controller
{
    public function index()
    {
        // Verificar si el usuario está autenticado y tiene el rol de estudiante y si tiene inscripciones aprobadas,OSEA SI YA SE APROBO SU COMPR0BANTE DE PAGO
        // $user = Auth::user();
        // if ($user && $user->estudiante) {
        //     $inscripcionAprobada = TutorEstudianteInscripcion::with('inscripcion')
        //         ->where('idEstudiante', $user->id)
        //         ->whereHas('inscripcion', function($query) {
        //             $query->where('status', 'aprobado');
        //         })
        //         ->exists();

        //     if ($inscripcionAprobada) {
        //         return redirect()->route('inscripcion.estudiante.imprimirFormularioInscripcion');
        //     }
        // }
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
                'estudiante.email AS estudiante_email', // Añadido el email del estudiante
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
                'tutor.tokenTutor AS tutor_token',
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
                'inscripcion.nombreApellidosTutor AS nombreApellidosTutor',
                'inscripcion.correoTutor AS correoTutor',
                
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
        
        // Verificar si existe una boleta para mostrar sus datos
        $boletaInfo = $this->verificarBoletaExistente($estudianteId);
        
        // Obtener todas las áreas para cada tutor con sus categorías
        $tutoresIds = $data->pluck('tutor_id')->unique()->values()->toArray();
        $todasAreasDeLosTutores = [];
        
        foreach ($tutoresIds as $tutorId) {
            $todasAreasDeLosTutores[$tutorId] = DB::table('tutorareadelegacion')
                ->select([
                    'area.idArea',
                    'area.nombre AS nombre_area',
                ])
                ->join('area', 'tutorareadelegacion.idArea', '=', 'area.idArea')
                ->where('tutorareadelegacion.id', $tutorId)
                ->get()
                ->map(function($area) use ($data) {
                    // Obtener las categorías para cada área usando la función categoriasPorArea
                    $categorias = $this->categoriasPorArea(
                        $data->first()->convocatoria_id, 
                        $area->idArea
                    );
                    
                    return [
                        'id' => $area->idArea,
                        'nombre' => $area->nombre_area,
                        'categorias' => $categorias
                    ];
                })
                ->toArray();
        }

        $totalTutores = $data->groupBy('tutor_ci')->count(); // <- Añade esto antes de procesar
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
                'email' => $data->first()->estudiante_email,
                'grado' => $data->first()->estudiante_grado,
                'fecha_nacimiento' => $data->first()->estudiante_nacimiento,
                'genero' => $data->first()->estudiante_genero
            ],
            
            'tutores' => $data->groupBy('tutor_ci')->map(function($tutorGroup) use ($todasAreasDeLosTutores, $totalTutores) { // <- Añade $totalTutores aquí
                $first = $tutorGroup->first();
                $tutorId = $first->tutor_id;
                return [
                    'id' => $tutorId,
                    'nombre' => $first->tutor_nombre,
                    'apellido_paterno' => $first->tutor_apellido_paterno,
                    'apellido_materno' => $first->tutor_apellido_materno,
                    'ci' => $first->tutor_ci,
                    'profesion' => $first->tutor_profesion,
                    'telefono' => $first->tutor_telefono,
                    'email' => $first->tutor_email,
                    'token' => $first->tutor_token,
                    // Áreas del tutor en la inscripción actual
                    'areas' => $totalTutores > 1 
                    ? [ // Si hay más de 1 tutor: solo la primera área
                        [
                            'id' => $first->area_id,
                            'nombre' => $first->area_nombre,
                            'categoria_id' => $first->categoria_id,
                            'categoria' => $first->categoria_nombre,
                            'detalle_inscripcion_id' => $first->detalle_inscripcion_id,
                            'fecha_registro' => $first->area_fecha_registro
                        ]
                    ]
                    : $tutorGroup->map(function($item) { // Un solo tutor: todas las áreas
                        return [
                            'id' => $item->area_id,
                            'nombre' => $item->area_nombre,
                            'categoria_id' => $item->categoria_id,
                            'categoria' => $item->categoria_nombre,
                            'detalle_inscripcion_id' => $item->detalle_inscripcion_id,
                            'fecha_registro' => $item->area_fecha_registro
                        ];
                    })->toArray(),
                    // Todas las áreas del tutor con sus categorías
                    'todas_areas' => $todasAreasDeLosTutores[$tutorId] ?? [],
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
                'grado_id' => $data->first()->grado_id,
                'nombre_apellidos_tutor' => $data->first()->nombreApellidosTutor,
                'correo_tutor' => $data->first()->correoTutor
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
            })->unique('detalle_inscripcion_id')->values()->toArray(), // Eliminar duplicados
            
            // Añadir todas las áreas de todos los tutores con sus categorías para facilitar su uso en la vista
            'todas_areas_tutores' => $todasAreasDeLosTutores
        ];

        // Calcular el total automáticamente
        $processed['totalPagar'] = array_sum(array_column($processed['inscripciones'], 'precio'));
        
        return view('inscripciones.FormularioDatosInscripcionEst', $processed);
    }

    public function categoriasPorArea($idConvocatoria, $idArea) 
    {
        $resultados = ConvocatoriaAreaCategoria::with('categoria:idCategoria,nombre')
            ->where('idConvocatoria', $idConvocatoria)
            ->where('idArea', $idArea)
            ->get();
        
        // Verificación para evitar errores si no encuentra datos
        if ($resultados->isEmpty()) {
            return collect(); // Devuelve colección vacía
        }

        return $resultados->map(function ($registro) {
            return [
                'id_categoria' => $registro->idCategoria,
                'nombre_categoria' => $registro->categoria->nombre ?? 'Sin nombre', // Seguro contra valores nulos
                'precios' => [
                    'individual' => $registro->precioIndividual,
                    'duo' => $registro->precioDuo,
                    'equipo' => $registro->precioEquipo,
                ],
                'area_id' => $registro->idArea,
                'convocatoria_id' => $registro->idConvocatoria
            ];
        });
    }

    
    /**
     * Verifica si existe una boleta para las inscripciones del estudiante
     * Devuelve datos de la boleta si existe, o valores nulos si no existe
     * 
     * @param int $estudianteId
     * @return array
     */
    private function verificarBoletaExistente($estudianteId)
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
            
            // Si no existe boleta, devolver valores nulos
            return [
                'codigoBoleta' => null,
                'fechaInicio' => null,
                'fechaFin' => null
            ];
        } 
        catch (\Exception $e) {
            Log::error('Error verificando boleta existente para estudiante:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // En caso de error, devolver valores nulos
            return [
                'codigoBoleta' => null,
                'fechaInicio' => null,
                'fechaFin' => null
            ];
        }
    }

    public function exportPdf()
    {
        $estudianteId = Auth::id();
    
        // Obtener datos del estudiante y sus inscripciones
        $data = DB::table('tutorestudianteinscripcion')
            ->select([
                // Datos del estudiante
                'estudiante.name AS estudiante_nombre',
                'estudiante.apellidoPaterno AS estudiante_apellido_paterno',
                'estudiante.apellidoMaterno AS estudiante_apellido_materno',
                'estudiante.ci AS estudiante_ci',
                'grado.grado AS estudiante_grado',
                
                // Datos del tutor
                'tutor_user.name AS tutor_nombre',
                'tutor_user.apellidoPaterno AS tutor_apellido_paterno',
                'tutor_user.apellidoMaterno AS tutor_apellido_materno',
                'tutor_user.ci AS tutor_ci',
                'tutor.profesion AS tutor_profesion',
                'delegacion.nombre AS tutor_colegio',
                
                // Datos de área/categoría
                'area.nombre AS area_nombre',
                'categoria.nombre AS categoria_nombre',
                
                // Precio (usamos precioIndividual como valor estático)
                DB::raw('15 AS precio') // Valor estático de 15 Bs para INDIVIDUAL
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
            ->where('tutorestudianteinscripcion.idEstudiante', $estudianteId)
            ->get();
    
        if ($data->isEmpty()) {
            return back()->with('error', 'No se encontraron inscripciones para generar la boleta');
        }
        
        // Generar código de orden de pago
        $codigoOrden = $this->generarCodigoOrdenPagoEstudiante($estudianteId);
        
        // Procesar los datos para la vista
        $processed = [
            'codigoOrden' => $codigoOrden,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            'fechaVencimiento' => now()->addMonth()->format('d/m/Y'),
            
            'estudiante' => [
                'nombre' => $data->first()->estudiante_nombre,
                'apellido_paterno' => $data->first()->estudiante_apellido_paterno,
                'apellido_materno' => $data->first()->estudiante_apellido_materno,
                'ci' => $data->first()->estudiante_ci,
                'grado' => $data->first()->estudiante_grado
            ],
            
            'tutores' => $data->groupBy('tutor_ci')->map(function($tutorGroup) {
                $first = $tutorGroup->first();
                return [
                    'nombre' => $first->tutor_nombre,
                    'apellido_paterno' => $first->tutor_apellido_paterno,
                    'apellido_materno' => $first->tutor_apellido_materno,
                    'ci' => $first->tutor_ci,
                    'profesion' => $first->tutor_profesion,
                    'areas' => $tutorGroup->pluck('area_nombre')->unique()->toArray(),
                    'colegio' => $first->tutor_colegio
                ];
            })->values()->toArray(),
            
            'inscripciones' => $data->map(function($item) {
                return [
                    'modalidad' => 'INDIVIDUAL', // Modalidad estática
                    'area' => $item->area_nombre,
                    'categoria' => $item->categoria_nombre,
                    'precio' => (float)$item->precio // Precio estático de 15 Bs
                ];
            })->toArray(),
        ];
    
        // Calcular el total automáticamente
        $processed['totalPagar'] = array_sum(array_column($processed['inscripciones'], 'precio'));
        
        // Configurar nombre del archivo
        $nombreArchivo = 'Orden_de_Pago_'.$processed['codigoOrden'].'.pdf';
        
        // Generar PDF
        return PDF::loadView('inscripciones.pdfOPV2222', $processed)
                ->download($nombreArchivo);
    }

    /**
     * Genera un código de orden de pago basado en el ID del estudiante
     * Verifica primero si ya existe una boleta para las inscripciones del estudiante
     * 
     * @param int $estudianteId
     * @return string
     */
    private function generarCodigoOrdenPagoEstudiante($estudianteId)
    {
        // Verificar primero si el estudiante tiene inscripciones
        $inscripcionesIds = TutorEstudianteInscripcion::where('idEstudiante', $estudianteId)
            ->pluck('idInscripcion');
        
        if ($inscripcionesIds->isEmpty()) {
            Log::error('El estudiante no tiene inscripciones asociadas', ['estudianteId' => $estudianteId]);
            throw new \Exception("El estudiante no tiene inscripciones registradas");
        }

        DB::beginTransaction();
        
        try {
            // Buscar boleta existente SOLO si está activa (fechafin >= hoy)
            $boletaExistente = BoletaPagoInscripcion::whereIn('idInscripcion', $inscripcionesIds)
                ->whereHas('boletaPago', function($query) {
                    $query->where('fechafin', '>=', now()->format('Y-m-d'));
                })
                ->with('boletaPago')
                ->first();

            if ($boletaExistente && $boletaExistente->boletaPago) {
                DB::commit();
                return $boletaExistente->boletaPago->CodigoBoleta;
            }

            // Crear nueva boleta
            $codigoBoleta = 'OP-' . str_pad($estudianteId, 6, '0', STR_PAD_LEFT);

            $boleta = BoletaPago::create([
                'CodigoBoleta' => $codigoBoleta,
                'MontoBoleta' => 30, // Valor estático como en tu código original
                'fechainicio' => now()->format('Y-m-d'),
                'fechafin' => now()->addMonth()->format('Y-m-d'),
                'estado' => 'pendiente' // Asegúrate de que tu tabla tenga este campo
            ]);

            // Registrar en boletapagoinscripcion
            foreach ($inscripcionesIds as $inscripcionId) {
                BoletaPagoInscripcion::create([
                    'idBoleta' => $boleta->idBoleta,
                    'idInscripcion' => $inscripcionId
                ]);
                
                // Registrar en verificacioninscripcion
                VerificacionInscripcion::create([
                    'idInscripcion' => $inscripcionId,
                    'idBoleta' => $boleta->idBoleta
                ]);
            }

            DB::commit();
            
            Log::info('Nueva boleta creada', [
                'boletaId' => $boleta->idBoleta,
                'codigo' => $codigoBoleta,
                'inscripciones' => $inscripcionesIds
            ]);
            
            return $codigoBoleta;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al generar boleta de pago', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'estudianteId' => $estudianteId
            ]);
            
            throw $e; // Relanzar la excepción para manejo superior
        }
    }
    
    /**
     * Método para mostrar el formulario de impresión de inscripción
     * 
     * @return \Illuminate\View\View
     */
    public function ImprimirFormularioInscripcion()
    {
        $estudianteId = Auth::id();

        $data = DB::table('tutorestudianteinscripcion')
            ->select([
                'estudiante.id AS estudiante_id',
                'tutor.id AS tutor_id',
                'inscripcion.idInscripcion AS inscripcion_id',
                'convocatoria.idConvocatoria AS convocatoria_id',
                'area.idArea AS area_id',
                'categoria.idCategoria AS categoria_id',
                'delegacion.idDelegacion AS delegacion_id',
                'grado.idGrado AS grado_id',

                'estudiante.name AS estudiante_nombre',
                'estudiante.apellidoPaterno AS estudiante_apellido_paterno',
                'estudiante.apellidoMaterno AS estudiante_apellido_materno',
                'estudiante.ci AS estudiante_ci',
                'grado.grado AS estudiante_grado',
                'estudiante.fechaNacimiento AS estudiante_nacimiento',
                'estudiante.genero AS estudiante_genero',

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

                'area.nombre AS area_nombre',
                'categoria.nombre AS categoria_nombre',
                'detalle_inscripcion.created_at AS area_fecha_registro',
                'detalle_inscripcion.idDetalleInscripcion AS detalle_inscripcion_id',

                'convocatoria.nombre AS convocatoria_nombre',
                'convocatoria.fechaFin AS convocatoria_fecha_limite',
                'convocatoria.metodoPago AS convocatoria_metodo_pago',
                'convocatoria.contacto AS convocatoria_contacto',

                'inscripcion.fechaInscripcion AS inscripcion_fecha',
                'inscripcion.numeroContacto AS inscripcion_numero_contacto',
                'inscripcion.status AS inscripcion_status',

                DB::raw('15 AS precio'),
                DB::raw("'INDIVIDUAL' AS modalidad")
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
            ->where('convocatoria.estado', 'Publicada')
            ->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'No se encontraron inscripciones para mostrar la boleta');
        }

        $boletaInfo = $this->verificarBoletaExistente($estudianteId);
        
        // Crear el código de inscripción con formato ID-000000INSCRIPCION-ID
        $inscripcionId = $data->first()->inscripcion_id;
        $CodigoInscripcionEst = 'ID-' . str_pad($inscripcionId, 6, '0', STR_PAD_LEFT) ;

        $processed = [
            'codigoOrden' => $boletaInfo['codigoBoleta'],
            'fechaGeneracion' => $boletaInfo['fechaInicio'],
            'fechaVencimiento' => $boletaInfo['fechaFin'],
            'codigoInscripcion' => $CodigoInscripcionEst, // Nueva variable añadida

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

        $processed['totalPagar'] = array_sum(array_column($processed['inscripciones'], 'precio'));

        return view('inscripciones.ImprimirFormularioDeInscripcion', $processed);
    }

    public function PDFImprimirFormulario()
    {
        $estudianteId = Auth::id();

        $data = DB::table('tutorestudianteinscripcion')
            ->select([
                'estudiante.id AS estudiante_id',
                'tutor.id AS tutor_id',
                'inscripcion.idInscripcion AS inscripcion_id',
                'convocatoria.idConvocatoria AS convocatoria_id',
                'area.idArea AS area_id',
                'categoria.idCategoria AS categoria_id',
                'delegacion.idDelegacion AS delegacion_id',
                'grado.idGrado AS grado_id',

                'estudiante.name AS estudiante_nombre',
                'estudiante.apellidoPaterno AS estudiante_apellido_paterno',
                'estudiante.apellidoMaterno AS estudiante_apellido_materno',
                'estudiante.ci AS estudiante_ci',
                'grado.grado AS estudiante_grado',
                'estudiante.fechaNacimiento AS estudiante_nacimiento',
                'estudiante.genero AS estudiante_genero',

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

                'area.nombre AS area_nombre',
                'categoria.nombre AS categoria_nombre',
                'detalle_inscripcion.created_at AS area_fecha_registro',
                'detalle_inscripcion.idDetalleInscripcion AS detalle_inscripcion_id',

                'convocatoria.nombre AS convocatoria_nombre',
                'convocatoria.fechaFin AS convocatoria_fecha_limite',
                'convocatoria.metodoPago AS convocatoria_metodo_pago',
                'convocatoria.contacto AS convocatoria_contacto',

                'inscripcion.fechaInscripcion AS inscripcion_fecha',
                'inscripcion.numeroContacto AS inscripcion_numero_contacto',
                'inscripcion.status AS inscripcion_status',

                DB::raw('15 AS precio'),
                DB::raw("'INDIVIDUAL' AS modalidad")
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
            ->where('convocatoria.estado', 'Publicada')
            ->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'No se encontraron inscripciones para mostrar la boleta');
        }

        $boletaInfo = $this->verificarBoletaExistente($estudianteId);
        
        // Crear el código de inscripción con formato ID-000000INSCRIPCION-ID
        $inscripcionId = $data->first()->inscripcion_id;
        $CodigoInscripcionEst = 'ID-' . str_pad($inscripcionId, 6, '0', STR_PAD_LEFT) ;

        $processed = [
            'codigoOrden' => $boletaInfo['codigoBoleta'],
            'fechaGeneracionFormulario' => now()->format('d/m/Y H:i'),
            'fechaGeneracion' => $boletaInfo['fechaInicio'],
            'fechaVencimiento' => $boletaInfo['fechaFin'],
            'codigoInscripcion' => $CodigoInscripcionEst, // Nueva variable añadida

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

        $processed['totalPagar'] = array_sum(array_column($processed['inscripciones'], 'precio'));

        $nombreArchivo = 'Formulario_Inscripcion_'.$processed['codigoInscripcion'].'.pdf';
        
        // Generar PDF
        return PDF::loadView('inscripciones.PDFImprimirFormulario', $processed)
                ->download($nombreArchivo);
    }
    
}