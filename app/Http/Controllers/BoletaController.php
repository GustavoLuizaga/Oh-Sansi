<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BoletaController extends Controller
{
    public function procesarBoleta(Request $request)
    {
        // Validación personalizada
        $validator = Validator::make($request->all(), [
            'inscripcion_id' => 'required|integer|exists:verificacioninscripcion,idInscripcion',
            'codigo_comprobante' => [
                'required',
                'numeric',
                'digits:7',
                Rule::unique('verificacioninscripcion', 'CodigoComprobante')
                    ->whereNotNull('CodigoComprobante')
            ],
            'comprobantePago' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'estado_ocr' => 'required|in:1,2'
        ], [
            'codigo_comprobante.unique' => 'El comprobante ya ha sido registrado. Contacte con soporte técnico si es un error.',
            'comprobantePago.mimes' => 'Solo se permiten imágenes JPG, JPEG o PNG.',
            'comprobantePago.max' => 'El tamaño máximo permitido es 5MB.',
            'estado_ocr.in' => 'El comprobante no es válido.',
        ]);

        // Verificar si falló el OCR
        if ($request->estado_ocr == 2) {
            $validator->errors()->add(
                'ocr_error', 
                'No se detectó el número de comprobante. Suba una imagen nítida.'
            );
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // 1. Manejo de archivo
            $file = $request->file('comprobantePago');
            $inscripcionId = $request->inscripcion_id;
            $directory = "public/inscripcionID/{$inscripcionId}";
            $filename = $file->getClientOriginalName();
            
            // Limpiar directorio existente
            Storage::deleteDirectory($directory);
            Storage::makeDirectory($directory);
            
            // Guardar archivo
            $path = $file->storeAs($directory, $filename);

            // 2. Actualización en base de datos - tabla verificacioninscripcion
            $affected = DB::table('verificacioninscripcion')
                ->where('idInscripcion', $inscripcionId)
                ->update([
                    'CodigoComprobante' => $request->codigo_comprobante,
                    'RutaComprobante' => "storage/inscripcionID/{$inscripcionId}/{$filename}",
                    'Comprobante_valido' => 1, // Agregado: establecer Comprobante_valido a 1
                    'updated_at' => now()
                ]);

            if ($affected === 0) {
                throw new \Exception("No se encontró la inscripción especificada");
            }
            
            // 3. Actualización del campo status en la tabla inscripcion
            $statusUpdated = DB::table('inscripcion')
                ->where('idInscripcion', $inscripcionId)
                ->update([
                    'status' => 'aprobado', // Actualizado de "pendiente" a "aprobado"
                    
                ]);
                
            if ($statusUpdated === 0) {
                throw new \Exception("No se pudo actualizar el estado en la tabla inscripcion");
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Comprobante registrado exitosamente',
                'data' => [
                    'codigo' => $request->codigo_comprobante,
                    'ruta' => Storage::url($path)
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en BoletaController: ' . $e->getMessage());
            
            // Limpiar archivos en caso de error
            if (isset($path) && Storage::exists($path)) {
                Storage::delete($path);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error interno al procesar el comprobante: ' . $e->getMessage()
            ], 500);
        }
    }
}