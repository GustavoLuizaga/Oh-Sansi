<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GrupoInscripcion;
use App\Models\Delegacion;
use App\Models\Tutor;
use App\Models\TutorAreaDelegacion;
use App\Models\DetalleInscripcion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GrupoController extends Controller
{
    /**
     * Muestra la lista de grupos
     */
    public function index(Request $request)
    {
        $query = GrupoInscripcion::with('delegacion');
        
        // Filtrar por estado si se proporciona
        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
        }
        
        // Búsqueda por nombre de grupo o delegación
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombreGrupo', 'like', "%{$search}%")
                  ->orWhereHas('delegacion', function($q2) use ($search) {
                      $q2->where('nombre', 'like', "%{$search}%");
                  });
            });
        }
        
        // Verificar si el usuario es tutor
        $user = Auth::user();
        $esTutor = false;
        $idDelegacion = null;
        
        if ($user->tutor) {
            $esTutor = true;
            // Si es tutor, solo mostrar grupos de su delegación
            $tutorAreaDelegacion = TutorAreaDelegacion::where('id', $user->id)->first();
            if ($tutorAreaDelegacion) {
                $idDelegacion = $tutorAreaDelegacion->idDelegacion;
                $query->where('idDelegacion', $idDelegacion);
            }
        }
        
        $grupos = $query->orderBy('created_at', 'desc')->paginate(10);
        $delegaciones = Delegacion::orderBy('nombre')->get();
        
        return view('inscripciones.grupo', compact('grupos', 'delegaciones', 'esTutor', 'idDelegacion'));
    }
    
    /**
     * Almacena un nuevo grupo
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombreGrupo' => 'required|string|max:100',
            'modalidad' => 'required|in:duo,equipo',
            'idDelegacion' => 'required_if:esTutor,false|exists:delegacion,idDelegacion',
        ]);
        
        // Si es tutor, usar la delegación del tutor
        $user = Auth::user();
        $idDelegacion = $request->idDelegacion;
        
        if ($user->tutor) {
            $tutorAreaDelegacion = TutorAreaDelegacion::where('id', $user->id)->first();
            if ($tutorAreaDelegacion) {
                $idDelegacion = $tutorAreaDelegacion->idDelegacion;
            }
        }
        
        // Generar código de invitación único
        $codigoInvitacion = Str::random(8);
        
        $grupo = GrupoInscripcion::create([
            'codigoInvitacion' => $codigoInvitacion,
            'nombreGrupo' => $request->nombreGrupo,
            'modalidad' => $request->modalidad,
            'estado' => 'incompleto',
            'idDelegacion' => $idDelegacion
        ]);
        
        return redirect()->route('inscripcion.grupos')
                         ->with('success', 'Grupo creado exitosamente. Código de invitación: ' . $codigoInvitacion);
    }
    
    /**
     * Actualiza el estado de un grupo
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:activo,incompleto,cancelado',
        ]);
        
        $grupo = GrupoInscripcion::findOrFail($id);
        $grupo->estado = $request->estado;
        $grupo->save();
        
        return redirect()->route('inscripcion.grupos')
                         ->with('success', 'Estado del grupo actualizado correctamente');
    }
    
    /**
     * Elimina un grupo
     */
    public function destroy($id)
    {
        $grupo = GrupoInscripcion::findOrFail($id);
        
        // Verificar si hay detalles de inscripción asociados
        $detallesCount = DetalleInscripcion::where('idGrupoInscripcion', $id)->count();
        
        if ($detallesCount > 0) {
            return redirect()->route('inscripcion.grupos')
                             ->with('error', 'No se puede eliminar el grupo porque tiene inscripciones asociadas');
        }
        
        $grupo->delete();
        
        return redirect()->route('inscripcion.grupos')
                         ->with('success', 'Grupo eliminado correctamente');
    }
    
    /**
     * Get groups by modality.
     *
     * @param  string  $modalidad
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerGruposPorModalidad($modalidad)
    {
        try {
            // Validate modality
            if (!in_array($modalidad, ['duo', 'equipo'])) {
                return response()->json([], 200);
            }
            
            // Get current user ID (tutor)
            $user = Auth::user();
            
            // Get tutor's delegation
            $tutorAreaDelegacion = TutorAreaDelegacion::where('id', $user->id)->first();
            
            if (!$tutorAreaDelegacion) {
                // Log for debugging
                \Illuminate\Support\Facades\Log::warning('No delegation found for tutor', ['user_id' => $user->id]);
                return response()->json([], 200);
            }
            
            $idDelegacion = $tutorAreaDelegacion->idDelegacion;
            
            // Log for debugging
            \Illuminate\Support\Facades\Log::info('Searching groups for delegation', [
                'idDelegacion' => $idDelegacion,
                'modalidad' => $modalidad
            ]);
            
            // Get active groups with available capacity for the specified modality
            $grupos = GrupoInscripcion::where('modalidad', $modalidad)
                ->where('idDelegacion', $idDelegacion)
                ->where('estado', 'activo')
                ->select('id', 'nombreGrupo', 'codigoInvitacion')
                ->get();
            
            // Log for debugging
            \Illuminate\Support\Facades\Log::info('Groups found', ['count' => $grupos->count()]);
            
            return response()->json($grupos, 200);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error loading groups', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Error al cargar grupos: ' . $e->getMessage()
            ], 500);
        }
    }
}