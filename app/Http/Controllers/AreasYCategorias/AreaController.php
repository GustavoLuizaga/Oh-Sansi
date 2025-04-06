<?php

namespace App\Http\Controllers\AreasYCategorias;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AreaController extends Controller
{
    /**
     * Muestra la lista de áreas.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Area::query();

        // Búsqueda
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where('nombre', 'LIKE', "%{$searchTerm}%");
        }

        // Ordenamiento
        switch ($request->orderBy) {
            case 'nombre_asc':
                $query->orderBy('nombre', 'asc');
                break;
            case 'nombre_desc':
                $query->orderBy('nombre', 'desc');
                break;
            case 'todos':
            default:
                // No aplicar ningún orden específico
                break;
        }

        $areas = $query->get();

        return view('areas y categorias.gestionAreas', compact('areas'));
    }

    /**
     * Redirige a la vista principal ya que no se usa directamente.
     *
     * @return RedirectResponse
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('areas.index');
    }

    /**
     * Guarda una nueva área.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse|RedirectResponse
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|min:5|max:20|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $area = Area::create([
            'nombre' => $request->nombre,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Área creada exitosamente',
                'area' => $area
            ]);
        }

        return redirect()->route('areas.index')
            ->with('success', 'Área creada exitosamente');
    }

    /**
     * Muestra una sola área.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $area = Area::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'area' => $area
        ]);
    }

    /**
     * Devuelve los datos para editar una área (usado por AJAX).
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        $area = Area::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'area' => $area
        ]);
    }

    /**
     * Actualiza una área existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse|RedirectResponse
     */
    public function update(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|min:5|max:20|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $area = Area::findOrFail($id);
        $area->update([
            'nombre' => $request->nombre,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Área actualizada exitosamente',
                'area' => $area
            ]);
        }

        return redirect()->route('areas.index')
            ->with('success', 'Área actualizada exitosamente');
    }

    /**
     * Elimina una área.
     *
     * @param  int  $id
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(int $id): JsonResponse|RedirectResponse
    {
        $area = Area::findOrFail($id);
        $area->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Área eliminada exitosamente',
            ]);
        }

        return redirect()->route('areas.index')
            ->with('success', 'Área eliminada exitosamente');
    }
}
