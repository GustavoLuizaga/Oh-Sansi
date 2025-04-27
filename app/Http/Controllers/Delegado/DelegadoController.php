<?php

namespace App\Http\Controllers\Delegado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tutor;
use App\Models\User;
use App\Models\Delegacion;
use App\Models\TutorAreaDelegacion;
use App\Models\Area;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DelegadoController extends Controller
{
    /**
     * Muestra la lista de tutores con filtros y ordenamiento
     */
    public function index(Request $request)
    {
        // Consulta base para obtener tutores con sus relaciones
        $query = Tutor::with(['user', 'delegaciones'])
            ->join('users', 'tutor.id', '=', 'users.id')
            ->select('tutor.*')
            ->where('tutor.estado', 'aprobado'); // Solo mostrar tutores aprobados

        // Aplicar búsqueda si existe
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('apellidoPaterno', 'like', "%{$search}%")
                        ->orWhere('apellidoMaterno', 'like', "%{$search}%")
                        ->orWhere('ci', 'like', "%{$search}%");
                });
            });
        }

        // Aplicar ordenamiento
        $sort = $request->sort ?? 'name';
        $direction = $request->direction ?? 'asc';

        switch ($sort) {
            case 'ci':
                $query->join('users as u', 'tutor.id', '=', 'u.id')
                      ->orderBy('u.ci', $direction);
                break;
            case 'name':
                $query->join('users as u', 'tutor.id', '=', 'u.id')
                      ->orderBy('u.name', $direction);
                break;
            case 'colegio':
                // Ordenar por el nombre del colegio requiere un enfoque diferente
                // debido a la relación muchos a muchos
                $query->join('tutorAreaDelegacion as tad', 'tutor.id', '=', 'tad.id')
                      ->join('delegacion as d', 'tad.idDelegacion', '=', 'd.idDelegacion')
                      ->orderBy('d.nombre', $direction);
                break;
            default:
                $query->join('users as u', 'tutor.id', '=', 'u.id')
                      ->orderBy('u.name', $direction);
                break;
        }

        // Obtener resultados paginados
        $tutores = $query->distinct()->paginate(10);
        
        // Obtener la lista de colegios para el filtro
        $colegios = Delegacion::select('idDelegacion as id', 'nombre')->orderBy('nombre')->get();

        return view('delegado.delegado', compact('tutores', 'colegios'));
    }
    
    /**
     * Muestra la lista de solicitudes de tutores pendientes
     */
    public function solicitudes(Request $request)
    {
        // Consulta base para obtener tutores pendientes con sus relaciones
        $query = Tutor::with(['user', 'delegaciones', 'areas', 'tutorAreaDelegacion'])
            ->join('users', 'tutor.id', '=', 'users.id')
            ->select('tutor.*')
            ->where('tutor.estado', 'pendiente'); // Solo mostrar tutores pendientes

        // Aplicar búsqueda si existe
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('apellidoPaterno', 'like', "%{$search}%")
                        ->orWhere('apellidoMaterno', 'like', "%{$search}%")
                        ->orWhere('ci', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        // Aplicar ordenamiento
        $sort = $request->sort ?? 'name';
        $direction = $request->direction ?? 'asc';

        switch ($sort) {
            case 'ci':
                $query->join('users as u', 'tutor.id', '=', 'u.id')
                      ->orderBy('u.ci', $direction);
                break;
            case 'name':
                $query->join('users as u', 'tutor.id', '=', 'u.id')
                      ->orderBy('u.name', $direction);
                break;
            case 'email':
                $query->join('users as u', 'tutor.id', '=', 'u.id')
                      ->orderBy('u.email', $direction);
                break;
            case 'colegio':
                $query->join('tutorAreaDelegacion as tad', 'tutor.id', '=', 'tad.id')
                      ->join('delegacion as d', 'tad.idDelegacion', '=', 'd.idDelegacion')
                      ->orderBy('d.nombre', $direction);
                break;
            default:
                $query->join('users as u', 'tutor.id', '=', 'u.id')
                      ->orderBy('u.name', $direction);
                break;
        }

        // Obtener resultados paginados
        $solicitudes = $query->distinct()->paginate(10);

        return view('delegado.solicitud', compact('solicitudes'));
    }
    
    /**
     * Aprobar una solicitud de tutor
     */
    public function aprobarSolicitud($id)
    {
        try {
            $tutor = Tutor::findOrFail($id);
            $tutor->estado = 'aprobado';
            $tutor->save();
            
            return redirect()->route('delegado.solicitudes')
                ->with('success', 'Solicitud de tutor aprobada correctamente');
        } catch (\Exception $e) {
            return redirect()->route('delegado.solicitudes')
                ->with('error', 'Error al aprobar la solicitud: ' . $e->getMessage());
        }
    }
    
    /**
     * Rechazar una solicitud de tutor
     */
    public function rechazarSolicitud($id)
    {
        try {
            $tutor = Tutor::findOrFail($id);
            $tutor->estado = 'rechazado';
            $tutor->save();
            
            return redirect()->route('delegado.solicitudes')
                ->with('success', 'Solicitud de tutor rechazada correctamente');
        } catch (\Exception $e) {
            return redirect()->route('delegado.solicitudes')
                ->with('error', 'Error al rechazar la solicitud: ' . $e->getMessage());
        }
    }
    
    /**
     * Ver detalles de una solicitud de tutor
     */
    public function verSolicitud($id)
    {
        try {
            $tutor = Tutor::with(['user', 'delegaciones', 'areas'])->findOrFail($id);
            return view('delegado.ver-solicitud', compact('tutor'));
        } catch (\Exception $e) {
            return redirect()->route('delegado.solicitudes')
                ->with('error', 'Error al ver la solicitud: ' . $e->getMessage());
        }
    }
    
    /**
     * Cambiar el estado de director de un tutor
     */
    public function toggleDirector($id, Request $request)
    {
        try {
            $tutor = Tutor::findOrFail($id);
            $tutor->es_director = $request->has('es_director');
            $tutor->save();
            
            return redirect()->route('delegado.ver-solicitud', $id)
                ->with('success', 'Estado de director actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('delegado.ver-solicitud', $id)
                ->with('error', 'Error al actualizar el estado de director: ' . $e->getMessage());
        }
    }
    
    /**
     * Eliminar un tutor (solo el registro de tutor, no el usuario)
     */
    public function eliminarTutor($id)
    {
        try {
            // Buscar el tutor
            $tutor = Tutor::findOrFail($id);
            
            // Eliminar las relaciones en tutorAreaDelegacion
            TutorAreaDelegacion::where('id', $id)->delete();
            
            // Eliminar el registro de tutor
            $tutor->delete();
            
            return redirect()->route('delegado')
                ->with('success', 'Tutor eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('delegado')
                ->with('error', 'Error al eliminar el tutor: ' . $e->getMessage());
        }
    }
    
    /**
     * Ver detalles de un tutor aprobado
     */
    public function verDelegado($id)
    {
        try {
            // Obtener el tutor con sus relaciones
            $tutor = Tutor::with(['user', 'delegaciones', 'areas', 'tutorAreaDelegacion'])
                ->where('estado', 'aprobado')
                ->findOrFail($id);
                
            return view('delegado.verDelegado', compact('tutor'));
        } catch (\Exception $e) {
            return redirect()->route('delegado')
                ->with('error', 'Error al ver los detalles del tutor: ' . $e->getMessage());
        }
    }
}