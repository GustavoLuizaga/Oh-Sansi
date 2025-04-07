<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DelegacionController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('delegacion');
        
        // Search by name or SIE code
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('codigo_sie', 'like', '%' . $search . '%');
            });
        }
        
        // Filter by department
        if ($request->has('departamento') && !empty($request->departamento)) {
            $query->where('departamento', $request->departamento);
        }
        
        // Filter by province
        if ($request->has('provincia') && !empty($request->provincia)) {
            $query->where('provincia', $request->provincia);
        }
        
        // Filter by municipality
        if ($request->has('municipio') && !empty($request->municipio)) {
            $query->where('municipio', $request->municipio);
        }
        
        // Filter by dependencia
        if ($request->has('dependencia') && !empty($request->dependencia)) {
            $query->where('dependencia', $request->dependencia);
        }
        
        $delegaciones = $query->paginate(10);
        
        return view('delegaciones.delegaciones', compact('delegaciones'));
    }

    public function create()
    {
        return view('delegaciones.agregarDelegacion');
    }

    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'codigo_sie' => 'required|string|max:20|unique:delegacion',
            'nombre' => 'required|string|max:100|unique:delegacion',
            'dependencia' => 'required|in:Fiscal,Convenio,Privado,Comunitaria',
            'departamento' => 'required|string',
            'provincia' => 'required|string|max:20',
            'municipio' => 'required|string|max:20',
            'zona' => 'nullable|string|max:30',
            'direccion' => 'required|string|max:40',
            'telefono' => 'nullable|numeric',
            'nombre_responsable' => 'required|string|max:40',
            'correo_responsable' => 'required|email|unique:delegacion,responsable_email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Insertar en la base de datos
        DB::table('delegacion')->insert([
            'codigo_sie' => $request->codigo_sie,
            'nombre' => $request->nombre,
            'dependencia' => $request->dependencia,
            'departamento' => $request->departamento,
            'provincia' => $request->provincia,
            'municipio' => $request->municipio,
            'zona' => $request->zona,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'responsable_nombre' => $request->nombre_responsable,
            'responsable_email' => $request->correo_responsable,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('delegaciones')
            ->with('success', 'Colegio agregado correctamente');
    }

    public function show($codigo_sie)
    {
        $delegacion = DB::table('delegacion')->where('codigo_sie', $codigo_sie)->first();
        
        if (!$delegacion) {
            return redirect()->route('delegaciones')->with('error', 'Colegio no encontrado');
        }
        
        return view('delegaciones.ver', compact('delegacion'));
    }

    public function edit($codigo_sie)
    {
        $delegacion = DB::table('delegacion')->where('codigo_sie', $codigo_sie)->first();
        
        if (!$delegacion) {
            return redirect()->route('delegaciones')->with('error', 'Colegio no encontrado');
        }
        
        return view('delegaciones.editar', compact('delegacion'));
    }

    public function update(Request $request, $codigo_sie)
    {
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100|unique:delegacion,nombre,' . $codigo_sie . ',codigo_sie',
            'dependencia' => 'required|in:Fiscal,Convenio,Privado,Comunitaria',
            'departamento' => 'required|string',
            'provincia' => 'required|string|max:20',
            'municipio' => 'required|string|max:20',
            'zona' => 'required|string|max:30',
            'direccion' => 'required|string|max:40',
            'telefono' => 'required|numeric',
            'nombre_responsable' => 'required|string|max:40',
            'correo_responsable' => 'required|email|unique:delegacion,responsable_email,' . $codigo_sie . ',codigo_sie',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Actualizar en la base de datos
        DB::table('delegacion')
            ->where('codigo_sie', $codigo_sie)
            ->update([
                'nombre' => $request->nombre,
                'dependencia' => $request->dependencia,
                'departamento' => $request->departamento,
                'provincia' => $request->provincia,
                'municipio' => $request->municipio,
                'zona' => $request->zona,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'responsable_nombre' => $request->nombre_responsable,
                'responsable_email' => $request->correo_responsable,
                'updated_at' => now(),
            ]);

        return redirect()->route('delegaciones.ver', $codigo_sie)
            ->with('success', 'Colegio actualizado correctamente');
    }

    public function destroy($codigo_sie)
    {
        $deleted = DB::table('delegacion')->where('codigo_sie', $codigo_sie)->delete();
        
        return response()->json(['success' => true]);
    }

    // Add these new methods for exports
    public function exportPdf(Request $request)
    {
        $query = DB::table('delegacion');
        
        // Apply the same filters as in the index method
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('codigo_sie', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->has('departamento') && !empty($request->departamento)) {
            $query->where('departamento', $request->departamento);
        }
        
        if ($request->has('provincia') && !empty($request->provincia)) {
            $query->where('provincia', $request->provincia);
        }
        
        if ($request->has('municipio') && !empty($request->municipio)) {
            $query->where('municipio', $request->municipio);
        }
        
        if ($request->has('dependencia') && !empty($request->dependencia)) {
            $query->where('dependencia', $request->dependencia);
        }
        
        $delegaciones = $query->get();
        
        // Prepare filter information for the title
        $filterInfo = $this->getFilterInfo($request);
        
        // Generate PDF
        $pdf = PDF::loadView('delegaciones.pdf', compact('delegaciones', 'filterInfo'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('delegaciones.pdf');
    }
    
    public function exportExcel(Request $request)
    {
        $query = DB::table('delegacion');
        
        // Apply the same filters as in the index method
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('codigo_sie', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->has('departamento') && !empty($request->departamento)) {
            $query->where('departamento', $request->departamento);
        }
        
        if ($request->has('provincia') && !empty($request->provincia)) {
            $query->where('provincia', $request->provincia);
        }
        
        if ($request->has('municipio') && !empty($request->municipio)) {
            $query->where('municipio', $request->municipio);
        }
        
        if ($request->has('dependencia') && !empty($request->dependencia)) {
            $query->where('dependencia', $request->dependencia);
        }
        
        $delegaciones = $query->get();
        
        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->setCellValue('A1', 'Código SIE');
        $sheet->setCellValue('B1', 'Nombre de Colegio');
        $sheet->setCellValue('C1', 'Departamento');
        $sheet->setCellValue('D1', 'Provincia');
        $sheet->setCellValue('E1', 'Municipio');
        $sheet->setCellValue('F1', 'Dependencia');
        
        // Style headers
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A1:F1')->getFill()->getStartColor()->setARGB('FF0086CE'); // Primary color
        $sheet->getStyle('A1:F1')->getFont()->getColor()->setARGB('FFFFFFFF'); // White text
        
        // Add data
        $row = 2;
        foreach ($delegaciones as $delegacion) {
            $sheet->setCellValue('A' . $row, $delegacion->codigo_sie);
            $sheet->setCellValue('B' . $row, $delegacion->nombre);
            $sheet->setCellValue('C' . $row, $delegacion->departamento);
            $sheet->setCellValue('D' . $row, $delegacion->provincia);
            $sheet->setCellValue('E' . $row, $delegacion->municipio);
            $sheet->setCellValue('F' . $row, $delegacion->dependencia);
            
            // Alternate row colors
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->getStartColor()->setARGB('FFF8F9FA'); // Light gray
            }
            
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create the Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'delegaciones.xlsx';
        
        // Save to temp file
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);
        
        // Return the file for download
        return response()->download($temp_file, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
    
    // Helper method to get filter information for the title
    private function getFilterInfo(Request $request)
    {
        $filterInfo = [
            'title' => 'Registro de Colegios',
            'filters' => []
        ];
        
        if ($request->has('departamento') && !empty($request->departamento)) {
            $filterInfo['filters'][] = 'Departamento: ' . $request->departamento;
        }
        
        if ($request->has('provincia') && !empty($request->provincia)) {
            $filterInfo['filters'][] = 'Provincia: ' . $request->provincia;
        }
        
        if ($request->has('municipio') && !empty($request->municipio)) {
            $filterInfo['filters'][] = 'Municipio: ' . $request->municipio;
        }
        
        if ($request->has('dependencia') && !empty($request->dependencia)) {
            $filterInfo['filters'][] = 'Dependencia: ' . $request->dependencia;
        }
        
        if ($request->has('search') && !empty($request->search)) {
            $filterInfo['filters'][] = 'Búsqueda: ' . $request->search;
        }
        
        return $filterInfo;
    }
}