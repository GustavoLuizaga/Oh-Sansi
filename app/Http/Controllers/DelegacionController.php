<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    public function destroy($codigo_sie)
    {
        $deleted = DB::table('delegacion')->where('codigo_sie', $codigo_sie)->delete();
        
        if ($deleted) {
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false]);
    }
}