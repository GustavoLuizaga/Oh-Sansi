<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DelegacionController extends Controller
{
    public function index()
    {
        $delegaciones = DB::table('delegacion')->get();
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
}