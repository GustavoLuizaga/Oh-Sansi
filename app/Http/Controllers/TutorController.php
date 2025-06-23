<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TutorController extends Controller
{
    public function eliminarTutorPorToken($token)
    {
        try {
            // Buscar el usuario por token
            $user = DB::table('users')
                ->where('remember_token', $token)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token no válido'
                ], 404);
            }

            // Verificar que es un tutor
            $tutorExists = DB::table('tutor')
                ->where('id', $user->id)
                ->exists();

            if (!$tutorExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no es un tutor'
                ], 404);
            }

            DB::beginTransaction();

            $tutorId = $user->id;

            // Eliminar en orden correcto
            
            // 1. Obtener inscripciones relacionadas
            $inscripciones = DB::table('tutorestudianteinscripcion')
                ->where('idTutor', $tutorId)
                ->pluck('idInscripcion');

            // 2. Eliminar detalles de inscripción
            if ($inscripciones->isNotEmpty()) {
                DB::table('detalle_inscripcion')
                    ->whereIn('idInscripcion', $inscripciones)
                    ->delete();
            }

            // 3. Eliminar relación tutor-estudiante
            DB::table('tutorestudianteinscripcion')
                ->where('idTutor', $tutorId)
                ->delete();

            // 4. Eliminar áreas del tutor
            DB::table('tutorareadelegacion')
                ->where('id', $tutorId)
                ->delete();

            // 5. Eliminar inscripciones huérfanas
            if ($inscripciones->isNotEmpty()) {
                foreach ($inscripciones as $inscripcionId) {
                    $tieneOtrosTutores = DB::table('tutorestudianteinscripcion')
                        ->where('idInscripcion', $inscripcionId)
                        ->exists();
                    
                    if (!$tieneOtrosTutores) {
                        DB::table('inscripcion')
                            ->where('idInscripcion', $inscripcionId)
                            ->delete();
                    }
                }
            }

            // 6. Eliminar tutor
            DB::table('tutor')
                ->where('id', $tutorId)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tutor eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}