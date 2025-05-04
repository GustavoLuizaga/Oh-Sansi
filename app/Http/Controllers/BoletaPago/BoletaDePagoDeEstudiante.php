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
