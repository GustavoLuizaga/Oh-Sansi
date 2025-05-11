<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BoletaController extends Controller
{
    public function procesarBoleta(Request $request)
    {
        // Validar los datos recibidos
        $validated = $request->validate([
            'inscripcion_id' => 'required|integer|exists:verificacioninscripcion,idInscripcion',
            'codigo_comprobante' => 'required|numeric|digits:7',
            'comprobantePago' => 'required|file|mimes:jpg,jpeg,png|max:5120' // 5MB
        ]);

        try {
            DB::beginTransaction();

            // 1. Configurar rutas y directorio
            $file = $request->file('comprobantePago');
            $directory = "public/inscripcionID/{$request->inscripcion_id}";
            $filename = $file->getClientOriginalName(); // Nombre original
            $fullPath = "$directory/$filename";

            // 2. Limpiar directorio existente (eliminar todos los archivos)
            if (Storage::exists($directory)) {
                Storage::deleteDirectory($directory);
            }
            Storage::makeDirectory($directory);

            // 3. Guardar el archivo
            $path = $file->storeAs($directory, $filename);

            // 4. Actualizar la base de datos
            $affected = DB::table('verificacioninscripcion')
                ->where('idInscripcion', $request->inscripcion_id)
                ->update([
                    'CodigoComprobante' => $request->codigo_comprobante,
                    'RutaComprobante' => "storage/inscripcionID/{$request->inscripcion_id}/{$filename}",
                    'updated_at' => now()
                ]);

            if ($affected === 0) {
                throw new \Exception("No se encontró la inscripción especificada");
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Comprobante registrado correctamente',
                'codigo' => $request->codigo_comprobante,
                'path' => Storage::url($path)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar boleta: ' . $e->getMessage());
            
            // Limpiar en caso de error
            if (isset($fullPath) && Storage::exists($fullPath)) {
                Storage::delete($fullPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el comprobante: ' . $e->getMessage()
            ], 500);
        }
    }
}