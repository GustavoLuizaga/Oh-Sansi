<?php

namespace App\Http\Controllers\BoletaPago;

use App\Http\Controllers\Controller;
use App\Models\BoletaPago;
use Illuminate\Http\Request;
use App\Models\TutorEstudianteInscripcion;
use Illuminate\Support\Facades\Auth;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Log;
//use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\BoletaPagoInscripcion;

// use App\Models\Area;
// use App\Models\Categoria;
// use App\Models\Convocatoria;
// use App\Models\Grado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;


class BoletaDePago extends Controller
{
    public function index()
    {
        return view('boletapago.index');
    }

    public function ObtenerInscripcionesPorDelegadoArea()
    {
        $idTutor = Auth()->user()->id;
        $insc = new TutorEstudianteInscripcion();
        $inscripciones = $insc->obtenerInscripcionesPorTutor($idTutor);
        return response()->json($inscripciones);
    }


    public function datosParaLaBoleta($inscripciones) {}


    // En tu controlador
    public function obtenerEstudiantesInscritos()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                Log::warning('Usuario no autenticado');
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Primero obtienes las inscripciones usando el id del usuario actual
            $inscripciones = TutorEstudianteInscripcion::where('idTutor', $user->id)
                ->with(['inscripcion' => function ($query) {
                    $query->with([
                        'estudiantes' => function ($q) {
                            $q->with('user');
                        },
                        'area',    // Cargar relación con área
                        'grado'    // Cargar relación con grado
                    ]);
                }])
                ->get();

            if ($inscripciones->isEmpty()) {
                Log::info('No se encontraron inscripciones', ['tutor_id' => $user->id]);
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $datosFormateados = $inscripciones->map(function ($inscripcion) {
                $estudiante = $inscripcion->inscripcion->estudiantes->first();
                return [
                    'inscripcion' => $inscripcion->inscripcion,
                    'estudiante' => $estudiante,
                    'datos_personales' => $estudiante ? $estudiante->user : null
                ];
            })->filter();

            // Simplificar los datos usando el método auxiliar
            $datosSimplificados = $this->simplificarDatosEstudiantes($datosFormateados);

            Log::info('Estudiantes obtenidos correctamente', [
                'cantidad' => collect($datosSimplificados)->count()
            ]);

            return response()->json([
                'success' => true,
                'data' => $datosSimplificados
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener estudiantes:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los estudiantes: ' . $e->getMessage()
            ], 500);
        }
    }


    public function simplificarDatosEstudiantes($data)
    {
        return collect($data)->map(function ($item) {
            return [
                'inscripcion' => [
                    'id' => $item['inscripcion']['idInscripcion'],
                    'fecha' => $item['inscripcion']['fechaInscripcion'],
                    'grado' => [
                        'id' => $item['inscripcion']['idGrado'],
                        'nombre' => $item['inscripcion']['grado']['grado'] ?? 'No especificado'
                    ],
                    'area' => [
                        'id' => $item['inscripcion']['idArea'],
                        'nombre' => $item['inscripcion']['area']['nombre'] ?? 'No especificada'
                    ],
                    'categoria' => $item['inscripcion']['idCategoria']
                ],
                'estudiante' => [
                    'nombre' => $item['datos_personales']['name'],
                    'apellidoPaterno' => $item['datos_personales']['apellidoPaterno'],
                    'apellidoMaterno' => $item['datos_personales']['apellidoMaterno'],
                    'ci' => $item['datos_personales']['ci'],
                    'email' => $item['datos_personales']['email'],
                    'genero' => $item['datos_personales']['genero'],
                    'fechaNacimiento' => $item['datos_personales']['fechaNacimiento']
                ],
                'tutor' => [
                    'nombre' => $item['inscripcion']['nombreApellidosTutor'],
                    'correo' => $item['inscripcion']['correoTutor'],
                    'telefono' => $item['inscripcion']['numeroContacto']
                ]
            ];
        })->values()->all();
    }

    public function generarOrdenPago()
    {
        try {
            $user = Auth::user();
            $tutor = $user->tutor;

            // Verificar si ya existe una boleta para las inscripciones de este tutor
            $inscripcionesIds = TutorEstudianteInscripcion::where('idTutor', $user->id)
                ->select('idInscripcion')
                ->get()
                ->pluck('idInscripcion');

            // Buscar si ya existe una boleta para alguna de estas inscripciones
            $boletaExistente = BoletaPagoInscripcion::whereIn('idInscripcion', $inscripcionesIds)
                ->first();

            if ($boletaExistente) {
                // Si ya existe una boleta, obtener sus datos para el PDF
                $boleta = $boletaExistente->boletaPago;
                $codigoBoleta = $boleta->CodigoBoleta;
            } else {
                // Si no existe, crear nueva boleta
                $codigoBoleta = 'OP-' . str_pad($user->id, 6, '0', STR_PAD_LEFT);

                $boleta = BoletaPago::create([
                    'CodigoBoleta' => $codigoBoleta,
                    'MontoBoleta' => 15,
                    'fechainicio' => now()->format('Y-m-d'),
                    'fechafin' => now()->addMonth()->format('Y-m-d'),
                ]);

                // Crear las relaciones
                foreach ($inscripcionesIds as $inscripcionId) {
                    BoletaPagoInscripcion::create([
                        'idBoleta' => $boleta->idBoleta,
                        'idInscripcion' => $inscripcionId,
                    ]);
                }
            }

            // Obtener datos para el PDF
            $inscripciones = TutorEstudianteInscripcion::where('idTutor', $user->id)
                ->with([
                    'inscripcion.estudiantes.user',
                    'inscripcion.area',
                    'inscripcion.categoria',
                    'inscripcion.grado',
                ])
                ->get();

            // Agrupar inscripciones
            $inscripcionesAgrupadas = $inscripciones->groupBy([
                'inscripcion.area.nombre',
                'inscripcion.categoria.nombre'
            ]);

            // Calcular totales
            $totalGeneral = 0;
            $detallesPorArea = [];

            foreach ($inscripcionesAgrupadas as $area => $categorias) {
                foreach ($categorias as $categoria => $inscripciones) {
                    $cantidadEstudiantes = $inscripciones->count();
                    $totalCategoria = $cantidadEstudiantes * 15; // Precio fijo de 15
                    $totalGeneral += $totalCategoria;

                    $detallesPorArea[] = [
                        'area' => $area,
                        'categoria' => $categoria,
                        'cantidad' => $cantidadEstudiantes,
                        'total' => $totalCategoria
                    ];
                }
            }

            $data = [
                'fecha' => now()->format('d/m/Y'),
                'codigoOrden' => $codigoBoleta,
                'tutor' => [
                    'nombre' => $user->name,
                    'apellidoPaterno' => $user->apellidoPaterno,
                    'apellidoMaterno' => $user->apellidoMaterno,
                    'ci' => $user->ci,
                    'profesion' => $tutor->profesion,
                    'areas' => $tutor->areasSimple()->pluck('nombre')->implode(', '),
                    'colegio' => 'Unidad Educativa ' . $tutor->colegio
                ],
                'inscripciones' => $inscripcionesAgrupadas,
                'detalles' => $detallesPorArea,
                'totalGeneral' => $totalGeneral
            ];

            $pdf = PDF::loadView('inscripciones.pdfLISTA-OP', $data);

            return $pdf->download('orden-de-pago.pdf');
        } catch (\Exception $e) {
            Log::error('Error generando orden de pago:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Error generando la orden de pago']);
        }
    }

    private function obtenerPrecioModalidad($modalidad)
    {
        return [
            'Individual' => 15,
            'Duo' => 15,
            'Grupal' => 15
        ][$modalidad] ?? 15;
    }

    public function exportPdf()
    {
        // para esa plantilla que me pasaste seria valido este data que le paso desde mi controlador???
        // $data = [
        //     'codigoOrden' => generarCodigoUnico(),
        //     'fechaGeneracion' => now()->format('d/m/Y H:i'),
            
        //     'estudiante' => [
        //         'nombre' => 'Kleberaso',
        //         'apellido_paterno' => 'Velasco',
        //         'apellido_materno' => 'Muruchi',
        //         'ci' => '9455600',
        //         'grado' => '1ro de Secundaria'
        //     ],
            
        //     'tutores' => [
        //         [
        //             'nombre' => 'Leonardo',
        //             'apellido_paterno' => 'Pérez',
        //             'apellido_materno' => 'Cayola',
        //             'ci' => '9485400',
        //             'profesion' => 'Ama de casa',
        //             'areas' => ['Informática', 'Biología', 'Matemáticas'],
        //             'colegio' => 'Unidad Educativa Tupack Katari'
        //         ],
        //         // ... más tutores si existen
        //     ],
            
        //     'inscripciones' => [
        //         [
        //             'modalidad' => 'INDIVIDUAL', // Siempre será este valor
        //             'area' => 'Informática',
        //             'categoria' => 'Londra',
        //             'precio' => 15 // Valor estático inventado
        //         ],
        //         [
        //             'modalidad' => 'INDIVIDUAL',
        //             'area' => 'Biología',
        //             'categoria' => 'Builders',
        //             'precio' => 20 // Valor estático inventado
        //         ]
        //     ],
            
        //     'totalPagar' => 35 // Suma de los precios estáticos (15 + 20)
        // ];



        $data = [
            'codigoOrden' => 'OP-000001', // Faltaban comillas en el string
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            
            'estudiante' => [
                'nombre' => 'Kleberaso',
                'apellido_paterno' => 'Velasco',
                'apellido_materno' => 'Muruchi',
                'ci' => '9455600',
                'grado' => '1ro de Secundaria'
            ],
            
            'tutores' => [
                [
                    'nombre' => 'Leonardo',
                    'apellido_paterno' => 'Pérez',
                    'apellido_materno' => 'Cayola',
                    'ci' => '9485400',
                    'profesion' => 'Ama de casa',
                    'areas' => ['Informática', 'Biología', 'Matemáticas'],
                    'colegio' => 'Unidad Educativa Tupack Katari'
                ],
                [
                    'nombre' => 'María',
                    'apellido_paterno' => 'Gómez',
                    'apellido_materno' => 'Fernández',
                    'ci' => '5874123',
                    'profesion' => 'Ingeniera de Sistemas',
                    'areas' => ['Física'],
                    'colegio' => 'Colegio San Agustín'
                ]
            ],
            
            'inscripciones' => [
                [
                    'modalidad' => 'INDIVIDUAL',
                    'area' => 'Informática',
                    'categoria' => 'Londra',
                    'precio' => 15
                ],
                [
                    'modalidad' => 'INDIVIDUAL',
                    'area' => 'Biología',
                    'categoria' => 'Builders',
                    'precio' => 20
                ]
            ],
            
            'totalPagar' => 35 // Esto debería calcularse automáticamente para evitar errores
        ];
    
        // Calcular el total automáticamente (mejor práctica)
        $data['totalPagar'] = array_sum(array_column($data['inscripciones'], 'precio'));
        
        // Configurar nombre del archivo
        $nombreArchivo = 'Orden_de_Pago_'.$data['codigoOrden'].'.pdf';
        
        // Generar PDF
        return PDF::loadView('inscripciones.pdfOPV2222', $data)
                ->download($nombreArchivo);
    }
}
