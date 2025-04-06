<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConvocatoriaController extends Controller
{
    /**
     * Display a listing of the convocatorias.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Fetch convocatorias from the database
        $convocatorias = DB::table('convocatoria')
            ->orderBy('created_at', 'desc')
            ->get();
        
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
            ]);
            
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
                        'precio' => 0, // Default price, can be updated later
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
        // In a real application, you would fetch the convocatoria from the database
        // For now, we'll use dummy data
        $convocatoria = [
            'id' => $id,
            'nombre' => 'Olimpiada Matemática 2024',
            'descripcion' => 'Competencia nacional de matemáticas para estudiantes de primaria y secundaria.',
            'fecha_inicio' => '2024-05-01',
            'fecha_fin' => '2024-06-30',
            'estado' => 'publicada',
            'costo_individual' => 50,
            'costo_duo' => 80,
            'costo_equipo' => 120,
            'contacto' => 'Email: olimpiada@example.com, Teléfono: +591 77777777',
            'requisitos' => [
                'Ser estudiante activo de una institución educativa',
                'Tener entre 8 y 18 años',
                'Contar con autorización de padres o tutores'
            ],
            'areas' => [
                [
                    'nombre' => 'Matemáticas',
                    'categorias' => [
                        [
                            'nombre' => 'Primaria',
                            'grados' => ['1ro', '2do', '3ro', '4to', '5to', '6to']
                        ],
                        [
                            'nombre' => 'Secundaria',
                            'grados' => ['1ro', '2do', '3ro', '4to', '5to', '6to']
                        ]
                    ]
                ]
            ]
        ];

        return view('convocatoria.ver', compact('convocatoria'));
    }

    /**
     * Show the form for editing the specified convocatoria.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // In a real application, you would fetch the convocatoria from the database
        // For now, we'll use dummy data
        $convocatoria = [
            'id' => $id,
            'nombre' => 'Olimpiada Matemática 2024',
            'descripcion' => 'Competencia nacional de matemáticas para estudiantes de primaria y secundaria.',
            'fecha_inicio' => '2024-05-01',
            'fecha_fin' => '2024-06-30',
            'estado' => 'publicada',
            'costo_individual' => 50,
            'costo_duo' => 80,
            'costo_equipo' => 120,
            'contacto' => 'Email: olimpiada@example.com, Teléfono: +591 77777777',
            'requisitos' => [
                'Ser estudiante activo de una institución educativa',
                'Tener entre 8 y 18 años',
                'Contar con autorización de padres o tutores'
            ],
            'areas' => [
                [
                    'nombre' => 'Matemáticas',
                    'categorias' => [
                        [
                            'nombre' => 'Primaria',
                            'grados' => ['1ro', '2do', '3ro', '4to', '5to', '6to']
                        ],
                        [
                            'nombre' => 'Secundaria',
                            'grados' => ['1ro', '2do', '3ro', '4to', '5to', '6to']
                        ]
                    ]
                ]
            ]
        ];

        return view('convocatoria.editar', compact('convocatoria'));
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
        // Validate the request
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'costo_individual' => 'nullable|numeric|min:0',
            'costo_duo' => 'nullable|numeric|min:0',
            'costo_equipo' => 'nullable|numeric|min:0',
            'contacto' => 'nullable|string',
            'requisitos' => 'nullable|array',
            'areas' => 'nullable|array',
        ]);

        // In a real application, you would update the convocatoria in the database
        // For now, we'll just log the data and redirect
        Log::info("Convocatoria $id actualizada", $validated);

        return redirect()->route('convocatoria')
            ->with('success', 'Convocatoria actualizada exitosamente.');
    }

    /**
     * Remove the specified convocatoria from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // In a real application, you would delete the convocatoria from the database
        // For now, we'll just log the action and redirect
        Log::info("Convocatoria $id eliminada");

        return redirect()->route('convocatoria')
            ->with('success', 'Convocatoria eliminada exitosamente.');
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
}