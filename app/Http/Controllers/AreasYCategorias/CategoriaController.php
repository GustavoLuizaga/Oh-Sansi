<?php

namespace App\Http\Controllers\AreasYCategorias;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Grado;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Muestra la vista de gestión de categorías
     */
    public function index()
    {
        $categorias = Categoria::with('grados')->get();
        $grados = Grado::all();
        
        return view('areas y categorias.gestionCategorias', compact('categorias', 'grados'));
    }
    
    /**
     * Almacena una nueva categoría con sus grados relacionados
     */
    public function store(Request $request)
{
    // Validación de entrada
    $request->validate([
        'nombreCategoria' => 'required|string|min:5|max:20|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
        'grados' => 'required|array|min:1',
        'grados.*' => 'required|exists:grado,idGrado',
    ]);

    // Normalización del nombre para evitar duplicados con variaciones de mayúsculas/minúsculas
    $nombreNormalizado = strtolower(trim($request->nombreCategoria));

    // Verificación de existencia previa en la base de datos
    $categoriaExistente = Categoria::whereRaw('LOWER(nombre) = ?', [$nombreNormalizado])->exists();

    if ($categoriaExistente) {
        return response()->json([
            'success' => false,
            'message' => 'Ya existe una categoría con este nombre o uno muy similar.',
        ], 422);
    }

    // Crear la nueva categoría
    $categoria = Categoria::create([
        'nombre' => $request->nombreCategoria
    ]);

    // Asociar los grados seleccionados
    $categoria->grados()->attach($request->grados);

    return response()->json([
        'success' => true,
        'message' => 'Categoría creada exitosamente',
        'categoria' => $categoria->load('grados')
    ]);
}
    
    /**
     * Obtiene datos para edición
     */
    public function edit($id)
    {
        $categoria = Categoria::with('grados')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'categoria' => $categoria
        ]);
    }
    
    /**
     * Actualiza una categoría existente
     */
    public function update(Request $request, $id)
    {
        // Validación
        $request->validate([
            'nombreCategoria' => 'required|string|min:5|max:20|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'grados' => 'required|array|min:1',
            'grados.*' => 'required|exists:grado,idGrado',
        ]);
        
        $categoria = Categoria::findOrFail($id);
        
        // Actualizar nombre
        $categoria->update([
            'nombre' => $request->nombreCategoria
        ]);
        
        // Sincronizar los grados (elimina los anteriores y agrega los nuevos)
        $categoria->grados()->sync($request->grados);
        
        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada exitosamente',
            'categoria' => $categoria->load('grados')
        ]);
    }
    
    /**
     * Elimina una categoría
     */
    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);
        
        // Al eliminar la categoría, se eliminarán automáticamente las relaciones en la tabla pivote
        $categoria->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada exitosamente'
        ]);
    }
}

