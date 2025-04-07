<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Convocatoria;

class ConvocatoriaController extends Controller
{
    /**
     * Display a listing of the convocatorias.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Verificar y actualizar el estado de las convocatorias vencidas
        $this->verificarEstadoConvocatorias();
        
        // Fetch convocatorias from the database
        $convocatorias = Convocatoria::all(); // sin orderBy

        
        return view('convocatoria.convocatoria', compact('convocatorias'));
    }

    /**
     * Show the form for creating a new convocatoria.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Fetch data from the database
        $areas = DB::table('area')->get();
        $categorias = DB::table('categoria')->get();
        
        // Get grades by category
        $gradosPorCategoria = [];
        $gradosCategorias = DB::table('gradoCategoria')
            ->join('grado', 'gradoCategoria.idGrado', '=', 'grado.idGrado')
            ->select('gradoCategoria.idCategoria', 'grado.idGrado', 'grado.grado')
            ->get();
        
        foreach ($gradosCategorias as $gc) {
            if (!isset($gradosPorCategoria[$gc->idCategoria])) {
                $gradosPorCategoria[$gc->idCategoria] = [];
            }
            $gradosPorCategoria[$gc->idCategoria][] = [
                'idGrado' => $gc->idGrado,
                'grado' => $gc->grado
            ];
        }
        
        // Pass data to the view
        return view('convocatoria.agregar', compact('areas', 'categorias', 'gradosPorCategoria'));
    }

    /**
     * Store a newly created convocatoria in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log the incoming request data for debugging
        Log::info('Convocatoria store request received', ['data' => $request->all()]);
        
        try {
            // Validate the request
            $validated = $request->validate([
                'nombre' => 'required|string|min:5|max:255',
                'descripcion' => 'required|string|min:10|max:1000',
                'fechaInicio' => 'required|date',
                'fechaFin' => 'required|date|after_or_equal:fechaInicio',
                'metodoPago' => 'required|string|max:100',
                'contacto' => 'required|string|min:10|max:255',
                'requisitos' => 'required|string|min:10|max:300',
                'areas' => 'required|array',
                'areas.*.idArea' => 'required|exists:area,idArea',
                'areas.*.categorias' => 'required|array',
                'areas.*.categorias.*.idCategoria' => 'required|exists:categoria,idCategoria',
                'areas.*.categorias.*.precio' => 'required|numeric|min:0',
            ]);
            
            // Verificar que la fecha fin no haya pasado
            $fechaFin = \Carbon\Carbon::parse($validated['fechaFin']);
            $hoy = \Carbon\Carbon::now();
            
            if ($fechaFin->lt($hoy)) {
                return back()->withInput()->with('error', 'No se puede crear una convocatoria con fecha fin ya pasada.');
            }
            
            Log::info('Validation passed', ['validated' => $validated]);
            
            DB::beginTransaction();
            
            // Crear la convocatoria
            $idConvocatoria = DB::table('convocatoria')->insertGetId([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'],
                'fechaInicio' => $validated['fechaInicio'],
                'fechaFin' => $validated['fechaFin'],
                'contacto' => $validated['contacto'],
                'requisitos' => $validated['requisitos'],
                'metodoPago' => $validated['metodoPago'],
                'estado' => 'Borrador',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            Log::info('Convocatoria created', ['idConvocatoria' => $idConvocatoria]);
            
            // Guardar las relaciones de áreas y categorías
            foreach ($validated['areas'] as $area) {
                $idArea = $area['idArea'];
                
                foreach ($area['categorias'] as $categoria) {
                    $idCategoria = $categoria['idCategoria'];
                    
                    // Guardar la relación convocatoria-área-categoría
                    DB::table('convocatoriaAreaCategoria')->insert([
                        'idConvocatoria' => $idConvocatoria,
                        'idArea' => $idArea,
                        'idCategoria' => $idCategoria,
                        'precio' => $categoria['precio'] ?? 0, // Usar el precio proporcionado o 0 como valor predeterminado
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            DB::commit();
            Log::info('Transaction committed successfully');
            
            return redirect()->route('convocatoria')->with('success', 'Convocatoria creada exitosamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear convocatoria: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Error al crear la convocatoria: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified convocatoria.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            // Obtener la convocatoria de la base de datos
            $convocatoria = DB::table('convocatoria')
                ->where('idConvocatoria', $id)
                ->first();
            
            if (!$convocatoria) {
                return redirect()->route('convocatoria')
                    ->with('error', 'Convocatoria no encontrada.');
            }
            
            // Obtener las áreas, categorías y grados asociados a la convocatoria
            $areasConCategorias = [];
            
            // Obtener las relaciones de convocatoria-área-categoría
            $convocatoriaAreasCategorias = DB::table('convocatoriaAreaCategoria')
                ->where('idConvocatoria', $id)
                ->get();
            
            // Agrupar por área
            $areaIds = $convocatoriaAreasCategorias->pluck('idArea')->unique();
            
            foreach ($areaIds as $areaId) {
                // Obtener información del área
                $areaInfo = DB::table('area')
                    ->where('idArea', $areaId)
                    ->first();
                
                if ($areaInfo) {
                    $area = (object) [
                        'idArea' => $areaInfo->idArea,
                        'nombre' => $areaInfo->nombre,
                        'categorias' => []
                    ];
                    
                    // Obtener categorías para esta área en esta convocatoria
                    $categoriaIds = $convocatoriaAreasCategorias
                        ->where('idArea', $areaId)
                        ->pluck('idCategoria')
                        ->unique();
                    
                    foreach ($categoriaIds as $categoriaId) {
                        // Obtener información de la categoría
                        $categoriaInfo = DB::table('categoria')
                            ->where('idCategoria', $categoriaId)
                            ->first();
                        
                        if ($categoriaInfo) {
                            $categoria = (object) [
                                'idCategoria' => $categoriaInfo->idCategoria,
                                'nombre' => $categoriaInfo->nombre,
                                'grados' => []
                            ];
                            
                            // Obtener grados para esta categoría
                            $grados = DB::table('gradoCategoria')
                                ->join('grado', 'gradoCategoria.idGrado', '=', 'grado.idGrado')
                                ->where('gradoCategoria.idCategoria', $categoriaId)
                                ->select('grado.idGrado', 'grado.grado as nombre')
                                ->get();
                            
                            $categoria->grados = $grados;
                            $area->categorias[] = $categoria;
                        }
                    }
                    
                    $areasConCategorias[] = $area;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error al obtener detalles de la convocatoria: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('convocatoria')
                ->with('error', 'Error al cargar los detalles de la convocatoria.');
        }

        return view('convocatoria.ver', compact('convocatoria', 'areasConCategorias'));
    }

    /**
     * Show the form for editing the specified convocatoria.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            // Obtener la convocatoria de la base de datos
            $convocatoria = DB::table('convocatoria')
                ->where('idConvocatoria', $id)
                ->first();
            
            if (!$convocatoria) {
                return redirect()->route('convocatoria')
                    ->with('error', 'Convocatoria no encontrada.');
            }
            
            // Obtener áreas para el formulario
            $areas = DB::table('area')->get();
            
            // Obtener categorías para el formulario
            $categorias = DB::table('categoria')->get();
            
            // Obtener las áreas, categorías y grados asociados a la convocatoria
            $areasConCategorias = [];
            
            // Obtener las relaciones de convocatoria-área-categoría
            $convocatoriaAreasCategorias = DB::table('convocatoriaAreaCategoria')
                ->where('idConvocatoria', $id)
                ->get();
            
            // Agrupar por área
            $areaIds = $convocatoriaAreasCategorias->pluck('idArea')->unique();
            
            foreach ($areaIds as $areaId) {
                // Obtener información del área
                $areaInfo = DB::table('area')
                    ->where('idArea', $areaId)
                    ->first();
                
                if ($areaInfo) {
                    $area = (object) [
                        'idArea' => $areaInfo->idArea,
                        'nombre' => $areaInfo->nombre,
                        'categorias' => []
                    ];
                    
                    // Obtener categorías para esta área en esta convocatoria
                    $categoriaIds = $convocatoriaAreasCategorias
                        ->where('idArea', $areaId)
                        ->pluck('idCategoria')
                        ->unique();
                    
                    foreach ($categoriaIds as $categoriaId) {
                        // Obtener información de la categoría
                        $categoriaInfo = DB::table('categoria')
                            ->where('idCategoria', $categoriaId)
                            ->first();
                        
                        if ($categoriaInfo) {
                            $categoria = (object) [
                                'idCategoria' => $categoriaInfo->idCategoria,
                                'nombre' => $categoriaInfo->nombre,
                                'grados' => []
                            ];
                            
                            // Obtener grados para esta categoría
                            $grados = DB::table('gradoCategoria')
                                ->join('grado', 'gradoCategoria.idGrado', '=', 'grado.idGrado')
                                ->where('gradoCategoria.idCategoria', $categoriaId)
                                ->select('grado.idGrado', 'grado.grado as nombre')
                                ->get();
                            
                            $categoria->grados = $grados;
                            $area->categorias[] = $categoria;
                        }
                    }
                    
                    $areasConCategorias[] = $area;
                }
            }
            
            // Get grades by category
            $gradosPorCategoria = [];
            $gradosCategorias = DB::table('gradoCategoria')
                ->join('grado', 'gradoCategoria.idGrado', '=', 'grado.idGrado')
                ->select('gradoCategoria.idCategoria', 'grado.idGrado', 'grado.grado')
                ->get();
            
            foreach ($gradosCategorias as $gc) {
                if (!isset($gradosPorCategoria[$gc->idCategoria])) {
                    $gradosPorCategoria[$gc->idCategoria] = [];
                }
                $gradosPorCategoria[$gc->idCategoria][] = [
                    'idGrado' => $gc->idGrado,
                    'grado' => $gc->grado
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Error al obtener datos para editar convocatoria: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('convocatoria')
                ->with('error', 'Error al cargar los datos para editar la convocatoria.');
        }

        return view('convocatoria.editar', compact('convocatoria', 'areas', 'categorias', 'areasConCategorias', 'gradosPorCategoria'));
    }

    /**
     * Update the specified convocatoria in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'fechaInicio' => 'required|date',
                'fechaFin' => 'required|date|after_or_equal:fechaInicio',
                'metodoPago' => 'required|string|max:100',
                'contacto' => 'required|string|min:10|max:255',
                'requisitos' => 'required|string|min:10|max:300',
                'areas.*.categorias.*.precio' => 'nullable|numeric|min:0',
            ]);
            
            // Verificar que la fecha fin no haya pasado si está en estado borrador
            if ($request->has('fechaFin')) {
                $fechaFin = \Carbon\Carbon::parse($validated['fechaFin']);
                $hoy = \Carbon\Carbon::now();
                
                if ($fechaFin->lt($hoy)) {
                    return back()->withInput()->with('error', 'No se puede actualizar la convocatoria con una fecha fin ya pasada.');
                }
            }
            
            // Obtener la convocatoria actual para verificar su estado
            $convocatoria = DB::table('convocatoria')
                ->where('idConvocatoria', $id)
                ->first();
                
            if (!$convocatoria) {
                return redirect()->route('convocatoria')
                    ->with('error', 'Convocatoria no encontrada.');
            }
            
            // Preparar los datos para actualizar
            $dataToUpdate = [
                'descripcion' => $validated['descripcion'],
                'contacto' => $validated['contacto'],
                'updated_at' => now(),
            ];
            
            // Si la convocatoria está en estado Borrador, permitir actualizar todos los campos
            if ($convocatoria->estado == 'Borrador') {
                $dataToUpdate['nombre'] = $validated['nombre'];
                $dataToUpdate['fechaInicio'] = $validated['fechaInicio'];
                $dataToUpdate['fechaFin'] = $validated['fechaFin'];
                $dataToUpdate['metodoPago'] = $validated['metodoPago'];
                $dataToUpdate['requisitos'] = $validated['requisitos'];
            }
            
            // Actualizar la convocatoria en la base de datos
            DB::table('convocatoria')
                ->where('idConvocatoria', $id)
                ->update($dataToUpdate);
            
            // Si la convocatoria está en estado Borrador y se enviaron áreas, actualizar las relaciones
            if ($convocatoria->estado == 'Borrador' && $request->has('areas')) {
                // Eliminar las relaciones existentes
                DB::table('convocatoriaAreaCategoria')
                    ->where('idConvocatoria', $id)
                    ->delete();
                
                // Guardar las nuevas relaciones
                foreach ($request->areas as $area) {
                    $idArea = $area['idArea'];
                    
                    if (isset($area['categorias'])) {
                        foreach ($area['categorias'] as $categoria) {
                            $idCategoria = $categoria['idCategoria'];
                            
                            // Guardar la relación convocatoria-área-categoría
                            DB::table('convocatoriaAreaCategoria')->insert([
                                'idConvocatoria' => $id,
                                'idArea' => $idArea,
                                'idCategoria' => $idCategoria,
                                'precio' => $categoria['precio'] ?? 0, // Usar el precio proporcionado o 0 como valor predeterminado
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
            
            return redirect()->route('convocatorias.ver', $id)
                ->with('success', 'Convocatoria actualizada exitosamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error al actualizar convocatoria: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Error al actualizar la convocatoria: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified convocatoria from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Obtener la convocatoria para verificar su estado
            $convocatoria = DB::table('convocatoria')
                ->where('idConvocatoria', $id)
                ->first();
                
            if (!$convocatoria) {
                return redirect()->route('convocatoria')
                    ->with('error', 'Convocatoria no encontrada.');
            }
            
            // Solo permitir eliminar convocatorias en estado Borrador o Cancelada
            if ($convocatoria->estado == 'Publicada') {
                return redirect()->route('convocatoria')
                    ->with('error', 'No se puede eliminar una convocatoria publicada. Debe cancelarla primero.');
            }
            
            // Eliminar las relaciones de áreas y categorías
            DB::table('convocatoriaAreaCategoria')
                ->where('idConvocatoria', $id)
                ->delete();
                
            // Eliminar la convocatoria
            DB::table('convocatoria')
                ->where('idConvocatoria', $id)
                ->delete();
            
            Log::info("Convocatoria $id eliminada");

            return redirect()->route('convocatoria')
                ->with('success', 'Convocatoria eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar convocatoria: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('convocatoria')
                ->with('error', 'Error al eliminar la convocatoria.');
        }
    }

    /**
     * Export convocatorias to PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf()
    {
        // In a real application, you would generate a PDF with the convocatorias
        // For now, we'll just log the action and redirect
        Log::info('Exportando convocatorias a PDF');

        return redirect()->route('convocatoria')
            ->with('info', 'La exportación a PDF se está procesando.');
    }

    /**
     * Export convocatorias to Excel.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportExcel()
    {
        // In a real application, you would generate an Excel file with the convocatorias
        // For now, we'll just log the action and redirect
        Log::info('Exportando convocatorias a Excel');

        return redirect()->route('convocatoria')
            ->with('info', 'La exportación a Excel se está procesando.');
    }
    
    /**
     * Publish the specified convocatoria.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function publicar($id)
    {
        try {
            // Obtener la convocatoria para verificar su estado y fechas
            $convocatoria = DB::table('convocatoria')
                ->where('idConvocatoria', $id)
                ->first();
                
            if (!$convocatoria) {
                return redirect()->route('convocatoria')
                    ->with('error', 'Convocatoria no encontrada.');
            }
            
            // Verificar que la fecha fin no haya pasado
            $fechaFin = \Carbon\Carbon::parse($convocatoria->fechaFin);
            $hoy = \Carbon\Carbon::now();
            
            if ($fechaFin->lt($hoy)) {
                return redirect()->route('convocatorias.ver', $id)
                    ->with('error', 'No se puede publicar una convocatoria con fecha fin ya pasada.');
            }
            
            // Verificar que no haya otra convocatoria publicada
            $convocatoriaPublicada = DB::table('convocatoria')
                ->where('estado', 'Publicada')
                ->where('idConvocatoria', '!=', $id)
                ->first();
                
            if ($convocatoriaPublicada) {
                return redirect()->route('convocatorias.ver', $id)
                    ->with('error', 'Ya existe una convocatoria publicada. Debe cancelar la convocatoria actual antes de publicar una nueva.');
            }
            
            // Actualizar el estado de la convocatoria a 'Publicada'
            DB::table('convocatoria')
                ->where('idConvocatoria', $id)
                ->update([
                    'estado' => 'Publicada',
                    'updated_at' => now()
                ]);
            
            return redirect()->route('convocatorias.ver', $id)
                ->with('success', 'Convocatoria publicada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al publicar convocatoria: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('convocatorias.ver', $id)
                ->with('error', 'Error al publicar la convocatoria.');
        }
    }
    
    /**
     * Cancel the specified convocatoria.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancelar($id)
    {
        try {
            // Actualizar el estado de la convocatoria a 'Cancelada'
            DB::table('convocatoria')
                ->where('idConvocatoria', $id)
                ->update([
                    'estado' => 'Cancelada',
                    'updated_at' => now()
                ]);
            
            return redirect()->route('convocatorias.ver', $id)
                ->with('success', 'Convocatoria cancelada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al cancelar convocatoria: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('convocatorias.ver', $id)
                ->with('error', 'Error al cancelar la convocatoria.');
        }
    }

    /**
     * Create a new version of a published convocatoria.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function nuevaVersion($id)
    {
        try {
            // Obtener la convocatoria original
            $convocatoriaOriginal = DB::table('convocatoria')
                ->where('idConvocatoria', $id)
                ->first();
            
            if (!$convocatoriaOriginal) {
                return redirect()->route('convocatoria')
                    ->with('error', 'Convocatoria no encontrada.');
            }
            
            // Verificar que la convocatoria esté publicada
            if ($convocatoriaOriginal->estado != 'Publicada') {
                return redirect()->route('convocatorias.ver', $id)
                    ->with('error', 'Solo se pueden crear nuevas versiones de convocatorias publicadas.');
            }
            
            // Crear una nueva convocatoria como copia de la original pero en estado Borrador
            $nuevaConvocatoriaId = DB::table('convocatoria')->insertGetId([
                'nombre' => $convocatoriaOriginal->nombre . ' (Nueva Versión)',
                'descripcion' => $convocatoriaOriginal->descripcion,
                'fechaInicio' => $convocatoriaOriginal->fechaInicio,
                'fechaFin' => $convocatoriaOriginal->fechaFin,
                'contacto' => $convocatoriaOriginal->contacto,
                'requisitos' => $convocatoriaOriginal->requisitos,
                'metodoPago' => $convocatoriaOriginal->metodoPago,
                'estado' => 'Borrador',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Copiar las relaciones de áreas y categorías
            $convocatoriaAreasCategorias = DB::table('convocatoriaAreaCategoria')
                ->where('idConvocatoria', $id)
                ->get();
            
            foreach ($convocatoriaAreasCategorias as $relacion) {
                DB::table('convocatoriaAreaCategoria')->insert([
                    'idConvocatoria' => $nuevaConvocatoriaId,
                    'idArea' => $relacion->idArea,
                    'idCategoria' => $relacion->idCategoria,
                    'precio' => $relacion->precio,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            return redirect()->route('convocatorias.editar', $nuevaConvocatoriaId)
                ->with('success', 'Se ha creado una nueva versión de la convocatoria. Puede editarla ahora.');
        } catch (\Exception $e) {
            Log::error('Error al crear nueva versión de convocatoria: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('convocatorias.ver', $id)
                ->with('error', 'Error al crear nueva versión de la convocatoria.');
        }
    }

    /**
     * Recuperar una convocatoria cancelada a estado borrador.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function recuperar($id)
    {
        try {
            // Obtener la convocatoria para verificar su estado
            $convocatoria = DB::table('convocatoria')
                ->where('idConvocatoria', $id)
                ->first();
                
            if (!$convocatoria) {
                return redirect()->route('convocatoria')
                    ->with('error', 'Convocatoria no encontrada.');
            }
            
            // Solo permitir recuperar convocatorias en estado Cancelada
            if ($convocatoria->estado != 'Cancelada') {
                return redirect()->route('convocatorias.ver', $id)
                    ->with('error', 'Solo se pueden recuperar convocatorias canceladas.');
            }
            
            // Actualizar el estado de la convocatoria a 'Borrador'
            DB::table('convocatoria')
                ->where('idConvocatoria', $id)
                ->update([
                    'estado' => 'Borrador',
                    'updated_at' => now()
                ]);
            
            return redirect()->route('convocatorias.ver', $id)
                ->with('success', 'Convocatoria recuperada exitosamente. Ahora está en estado borrador.');
        } catch (\Exception $e) {
            Log::error('Error al recuperar convocatoria: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('convocatorias.ver', $id)
                ->with('error', 'Error al recuperar la convocatoria.');
        }
    }
    
    /**
     * Verificar y actualizar el estado de las convocatorias según sus fechas.
     * - Cambia a 'Borrador' las convocatorias publicadas cuya fecha fin ya pasó
     */
    private function verificarEstadoConvocatorias()
    {
        try {
            $hoy = \Carbon\Carbon::now();
            
            // Buscar convocatorias publicadas con fecha fin pasada
            $convocatoriasVencidas = DB::table('convocatoria')
                ->where('estado', 'Publicada')
                ->where('fechaFin', '<', $hoy->format('Y-m-d'))
                ->get();
            
            // Actualizar el estado de las convocatorias vencidas a 'Borrador'
            foreach ($convocatoriasVencidas as $convocatoria) {
                DB::table('convocatoria')
                    ->where('idConvocatoria', $convocatoria->idConvocatoria)
                    ->update([
                        'estado' => 'Borrador',
                        'updated_at' => now()
                    ]);
                
                Log::info("Convocatoria {$convocatoria->idConvocatoria} cambió automáticamente a estado Borrador por fecha vencida");
            }
        } catch (\Exception $e) {
            Log::error('Error al verificar estado de convocatorias: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}