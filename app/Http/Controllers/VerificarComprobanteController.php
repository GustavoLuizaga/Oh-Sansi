<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Events\InscripcionRechazarComprobante;

class VerificarComprobanteController extends Controller
{
    public function index()
    {
        $query = "
            SELECT  
                tei.idEstudiante,
                tei.idTutor,
                tei.idInscripcion,
                CONCAT(u.name, ' ', u.apellidoPaterno, ' ', u.apellidoMaterno) AS nombre_completo, 
                bi.idBoleta, 
                a.nombre AS area_nombre, 
                i.status, 
                vi.Comprobante_valido, 
                vi.CodigoComprobante,           
                vi.RutaComprobante,
                vi.id as verificacion_id,
                CONCAT('" . url('/') . "', '/', vi.RutaComprobante) AS ruta_publica_para_usar_en_produccion,
                vi.created_at AS fecha_verificacion,  
                vi.updated_at AS fecha_actualizacion_verificacion  
            FROM 
                tutorestudianteinscripcion tei
            JOIN estudiante e 
                ON tei.idEstudiante = e.id
            JOIN users u 
                ON e.id = u.id
            JOIN inscripcion i 
                ON tei.idInscripcion = i.idInscripcion
            JOIN detalle_inscripcion di 
                ON di.idInscripcion = i.idInscripcion
            JOIN area a 
                ON di.idArea = a.idArea
            JOIN boletapagoinscripcion bi 
                ON bi.idInscripcion = i.idInscripcion
            JOIN verificacioninscripcion vi 
                ON vi.idInscripcion = i.idInscripcion
                AND vi.Comprobante_valido = 1
            ORDER BY bi.idBoleta, vi.CodigoComprobante;
        ";
        
        $results = DB::select($query);
        
        // Agrupar por idBoleta Y CodigoComprobante para generar filas únicas
        $comprobantes = collect($results)->groupBy(function($item) {
            return $item->idBoleta . '-' . $item->CodigoComprobante;
        });
        
        return view('inscripciones.VerificarComprobante', compact('comprobantes'));
    }
    
    /**
     * Servir archivo de comprobante directamente desde storage
     * Modificado para usar el ID de verificación específico
     */
    public function mostrarComprobante($idBoleta, $codigoComprobante = null, $verificacionId = null)
    {
        try {
            $query = DB::table('boletapagoinscripcion as bi')
                ->join('inscripcion as i', 'bi.idInscripcion', '=', 'i.idInscripcion')
                ->join('verificacioninscripcion as vi', 'vi.idInscripcion', '=', 'i.idInscripcion')
                ->where('bi.idBoleta', $idBoleta)
                ->where('vi.Comprobante_valido', '=', 1);
                
            // Si se proporciona el ID específico de verificación, usarlo
            if ($verificacionId) {
                $query->where('vi.id', $verificacionId);
            } else if ($codigoComprobante) {
                $query->where('vi.CodigoComprobante', $codigoComprobante);
            }
            
            $comprobante = $query->select('vi.RutaComprobante')->first();

            if (!$comprobante) {
                abort(404, 'Comprobante no encontrado');
            }

            // Ruta completa del archivo
            $rutaCompleta = storage_path('app/' . $comprobante->RutaComprobante);
            
            if (!file_exists($rutaCompleta)) {
                abort(404, 'Archivo no encontrado');
            }

            // Obtener el tipo MIME del archivo
            $mimeType = mime_content_type($rutaCompleta);
            
            // Servir el archivo directamente
            return response()->file($rutaCompleta, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=3600'
            ]);
            
        } catch (\Exception $e) {
            abort(500, 'Error al cargar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Aprobar comprobante específico por CodigoComprobante
     */
    public function aprobarComprobante($idBoleta, $codigoComprobante = null)
    {
        try {
            // Si no se especifica código de comprobante, aprobar todos los de la boleta
            if (!$codigoComprobante) {
                return $this->aprobarTodosComprobantes($idBoleta);
            }
            
            // Obtener inscripciones específicas para este código de comprobante
            $inscripciones = DB::table('boletapagoinscripcion as bi')
                ->join('verificacioninscripcion as vi', 'vi.idInscripcion', '=', 'bi.idInscripcion')
                ->where('bi.idBoleta', $idBoleta)
                ->where('vi.CodigoComprobante', $codigoComprobante)
                ->where('vi.Comprobante_valido', 1)
                ->pluck('bi.idInscripcion');
                
            // Actualizar el status a "aprobado" en las inscripciones específicas
            DB::table('inscripcion')
                ->whereIn('idInscripcion', $inscripciones)
                ->update([
                    'status' => 'aprobado',
                    'updated_at' => Carbon::now()
                ]);
                
            return response()->json([
                'success' => true,
                'message' => 'Comprobante aprobado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar el comprobante: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Rechazar comprobante específico por CodigoComprobante
     */
    public function rechazarComprobante($idBoleta, $codigoComprobante = null)
    {
        try {
            // Si no se especifica código de comprobante, rechazar todos los de la boleta
            if (!$codigoComprobante) {
                return $this->rechazarTodosComprobantes($idBoleta);
            }
            
            // Obtener inscripciones específicas para este código de comprobante
            $inscripciones = DB::table('boletapagoinscripcion as bi')
                ->join('verificacioninscripcion as vi', 'vi.idInscripcion', '=', 'bi.idInscripcion')
                ->where('bi.idBoleta', $idBoleta)
                ->where('vi.CodigoComprobante', $codigoComprobante)
                ->where('vi.Comprobante_valido', 1)
                ->pluck('bi.idInscripcion');
                
            // Actualizar el status a "rechazado" en las inscripciones específicas
            DB::table('inscripcion')
                ->whereIn('idInscripcion', $inscripciones)
                ->update([
                    'status' => 'rechazado',
                    'updated_at' => Carbon::now()
                ]);
                
            // Notificar a los usuarios afectados
            $notificados = [];
            foreach ($inscripciones as $idInscripcion) {
                $userId = DB::table('tutorestudianteinscripcion')
                    ->where('idInscripcion', $idInscripcion)
                    ->value('idEstudiante');

                // Clave única por usuario y comprobante
                $clave = $userId . '-' . $codigoComprobante;

                if (!isset($notificados[$clave])) {
                    event(new InscripcionRechazarComprobante(
                        $userId,
                        'Tu comprobante ha sido rechazado.',
                        'denegacion',
                        $codigoComprobante
                    ));
                    $notificados[$clave] = true;
                }
            }
                
            return response()->json([
                'success' => true,
                'message' => 'Comprobante rechazado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar el comprobante: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Métodos auxiliares para compatibilidad con el código anterior
    private function aprobarTodosComprobantes($idBoleta)
    {
        // Lógica original para aprobar todos los comprobantes de una boleta
        $inscripciones = DB::table('boletapagoinscripcion')
            ->where('idBoleta', $idBoleta)
            ->pluck('idInscripcion');
            
        DB::table('inscripcion')
            ->whereIn('idInscripcion', $inscripciones)
            ->update([
                'status' => 'aprobado',
                'updated_at' => Carbon::now()
            ]);
            
        return response()->json([
            'success' => true,
            'message' => 'Todos los comprobantes aprobados correctamente'
        ]);
    }
    
    private function rechazarTodosComprobantes($idBoleta)
    {
        // Lógica original para rechazar todos los comprobantes de una boleta
        $inscripciones = DB::table('boletapagoinscripcion')
            ->where('idBoleta', $idBoleta)
            ->pluck('idInscripcion');
            
        DB::table('inscripcion')
            ->whereIn('idInscripcion', $inscripciones)
            ->update([
                'status' => 'rechazado',
                'updated_at' => Carbon::now()
            ]);
            
        return response()->json([
            'success' => true,
            'message' => 'Todos los comprobantes rechazados correctamente'
        ]);
    }
}