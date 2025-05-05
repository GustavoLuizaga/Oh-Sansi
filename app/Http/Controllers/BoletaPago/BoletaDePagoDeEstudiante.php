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
use App\Models\Tutor;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Convocatoria;
use App\Models\Grado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;


class BoletaDePagoDeEstudiante extends Controller
{
    public function index()
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
        
        // Generar código de orden de pago
        $codigoOrden = $this->generarCodigoOrdenPagoEstudiante($estudianteId);
        
        // Procesar los datos para la vista
        $processed = [
            'codigoOrden' => $codigoOrden,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            
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
        
        return view('inscripciones.FormularioDatosInscripcionEst', $processed);
    }

    //Copiar este div en la vista donde quieras mostrar los datos que te da index
    // <div class="container mt-4">
    //     <!-- IDs Principales -->
    //     <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
    //         <h3>IDs Principales</h3>
    //         <p><strong>Estudiante ID:</strong> {{ $ids['estudiante_id'] }}</p>
    //         <p><strong>Tutor ID:</strong> {{ $ids['tutor_id'] }}</p>
    //         <p><strong>Inscripción ID:</strong> {{ $ids['inscripcion_id'] }}</p>
    //         <p><strong>Convocatoria ID:</strong> {{ $ids['convocatoria_id'] }}</p>
    //         <p><strong>Delegación ID:</strong> {{ $ids['delegacion_id'] }}</p>
    //         <p><strong>Grado ID:</strong> {{ $ids['grado_id'] }}</p>
    //     </div>

    //     <!-- Información del Estudiante -->
    //     <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
    //         <h3>Estudiante (ID: {{ $estudiante['id'] }})</h3>
    //         <p><strong>Nombre:</strong> {{ $estudiante['nombre'] }} {{ $estudiante['apellido_paterno'] }} {{ $estudiante['apellido_materno'] }}</p>
    //         <p><strong>CI:</strong> {{ $estudiante['ci'] }}</p>
    //         <p><strong>Grado:</strong> {{ $estudiante['grado'] }} (ID: {{ $inscripcion['grado_id'] }})</p>
    //         <p><strong>Fecha nacimiento:</strong> {{ $estudiante['fecha_nacimiento'] }}</p>
    //         <p><strong>Género:</strong> {{ $estudiante['genero'] }}</p>
    //     </div>

    //     <!-- Información de Tutores -->
    //     <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
    //         <h3>Tutores</h3>
    //         @foreach($tutores as $tutor)
    //         <div style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #ccc;">
    //             <p><strong>Tutor ID:</strong> {{ $tutor['id'] }}</p>
    //             <p><strong>Nombre:</strong> {{ $tutor['nombre'] }} {{ $tutor['apellido_paterno'] }} {{ $tutor['apellido_materno'] }}</p>
    //             <p><strong>CI:</strong> {{ $tutor['ci'] }}</p>
    //             <p><strong>Profesión:</strong> {{ $tutor['profesion'] }}</p>
    //             <p><strong>Teléfono:</strong> {{ $tutor['telefono'] }}</p>
    //             <p><strong>Email:</strong> {{ $tutor['email'] }}</p>
                
    //             <div style="margin-top: 10px;">
    //                 <h4>Áreas Inscritas</h4>
    //                 @foreach($tutor['areas'] as $area)
    //                 <p>
    //                     <strong>Área ID:</strong> {{ $area['id'] }} - {{ $area['nombre'] }} | 
    //                     <strong>Categoría ID:</strong> {{ $area['categoria_id'] }} - {{ $area['categoria'] }} | 
    //                     <strong>Detalle ID:</strong> {{ $area['detalle_inscripcion_id'] }} | 
    //                     <strong>Fecha:</strong> {{ $area['fecha_registro'] }}
    //                 </p>
    //                 @endforeach
    //             </div>
                
    //             <div style="margin-top: 10px;">
    //                 <h4>Colegio/Unidad</h4>
    //                 <p><strong>ID:</strong> {{ $tutor['colegio']['id'] }}</p>
    //                 <p><strong>Nombre:</strong> {{ $tutor['colegio']['nombre'] }}</p>
    //                 <p><strong>Dependencia:</strong> {{ $tutor['colegio']['dependencia'] }}</p>
    //                 <p><strong>Departamento:</strong> {{ $tutor['colegio']['departamento'] }}</p>
    //                 <p><strong>Provincia:</strong> {{ $tutor['colegio']['provincia'] }}</p>
    //                 <p><strong>Dirección:</strong> {{ $tutor['colegio']['direccion'] }}</p>
    //                 <p><strong>Teléfono:</strong> {{ $tutor['colegio']['telefono'] }}</p>
    //             </div>
    //         </div>
    //         @endforeach
    //     </div>

    //     <!-- Información de Convocatoria -->
    //     <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
    //         <h3>Convocatoria (ID: {{ $convocatoria['id'] }})</h3>
    //         <p><strong>Nombre:</strong> {{ $convocatoria['nombre'] }}</p>
    //         <p><strong>Fecha límite:</strong> {{ $convocatoria['fecha_limite'] }}</p>
    //         <p><strong>Método de pago:</strong> {{ $convocatoria['metodo_pago'] }}</p>
    //         <p><strong>Contacto:</strong> {{ $convocatoria['contacto'] }}</p>
    //     </div>

    //     <!-- Información de Inscripción -->
    //     <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
    //         <h3>Inscripción (ID: {{ $inscripcion['id'] }})</h3>
    //         <p><strong>Fecha:</strong> {{ $inscripcion['fecha'] }}</p>
    //         <p><strong>Número contacto:</strong> {{ $inscripcion['numero_contacto'] }}</p>
    //         <p><strong>Estado:</strong> {{ $inscripcion['status'] }}</p>
    //         <p><strong>Grado ID:</strong> {{ $inscripcion['grado_id'] }}</p>
    //     </div>

    //     <!-- Áreas Inscritas -->
    //     <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
    //         <h3>Áreas Inscritas</h3>
    //         @foreach($inscripciones as $inscripcion)
    //         <div style="margin-bottom: 10px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
    //             <p><strong>Área ID:</strong> {{ $inscripcion['area_id'] }} - {{ $inscripcion['area'] }}</p>
    //             <p><strong>Categoría ID:</strong> {{ $inscripcion['categoria_id'] }} - {{ $inscripcion['categoria'] }}</p>
    //             <p><strong>Detalle Inscripción ID:</strong> {{ $inscripcion['detalle_inscripcion_id'] }}</p>
    //             <p><strong>Modalidad:</strong> {{ $inscripcion['modalidad'] }}</p>
    //             <p><strong>Fecha registro:</strong> {{ $inscripcion['fecha_registro'] }}</p>
    //             <p><strong>Precio:</strong> Bs. {{ number_format($inscripcion['precio'], 2) }}</p>
    //         </div>
    //         @endforeach
    //         <p><strong>Total a pagar:</strong> Bs. {{ number_format($totalPagar, 2) }}</p>
    //     </div>

    //     <!-- Información General -->
    //     <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
    //         <h3>Información General</h3>
    //         <p><strong>Código de orden:</strong> {{ $codigoOrden ?? 'N/A' }}</p>
    //         <p><strong>Fecha generación:</strong> {{ $fechaGeneracion }}</p>
    //     </div>
    // </div>

    
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
        
        // Generar código de orden de pago usando la nueva función
        $codigoOrden = $this->generarCodigoOrdenPagoEstudiante($estudianteId);
        // Procesar los datos para la vista
        $processed = [
            'codigoOrden' => $codigoOrden,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            
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
        try {
            // Verificar si ya existe una boleta para las inscripciones de este estudiante
            $inscripcionesIds = TutorEstudianteInscripcion::where('idEstudiante', $estudianteId)
                ->select('idInscripcion')
                ->get()
                ->pluck('idInscripcion');

            // Buscar si ya existe una boleta para alguna de estas inscripciones
            $boletaExistente = BoletaPagoInscripcion::whereIn('idInscripcion', $inscripcionesIds)
                ->with('boletaPago')
                ->first();

            if ($boletaExistente && $boletaExistente->boletaPago) {
                // Si ya existe una boleta, retornar su código
                return $boletaExistente->boletaPago->CodigoBoleta;
            }

            // Si no existe, crear nuevo código
            $codigoBoleta = 'OP-' . str_pad($estudianteId, 6, '0', STR_PAD_LEFT);

            // Crear registro en la base de datos
            $boleta = BoletaPago::create([
                'CodigoBoleta' => $codigoBoleta,
                'MontoBoleta' => 15,
                'fechainicio' => now()->format('Y-m-d'),
                'fechafin' => now()->addMonth()->format('Y-m-d'),
            ]);

            // Crear las relaciones con las inscripciones
            foreach ($inscripcionesIds as $inscripcionId) {
                BoletaPagoInscripcion::create([
                    'idBoleta' => $boleta->idBoleta,
                    'idInscripcion' => $inscripcionId,
                ]);
            }

            return $codigoBoleta;

        } catch (\Exception $e) {
            Log::error('Error generando código de orden de pago para estudiante:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // En caso de error, devolver un código por defecto
            return 'OP-' . str_pad($estudianteId, 6, '0', STR_PAD_LEFT);
        }
    }
}
