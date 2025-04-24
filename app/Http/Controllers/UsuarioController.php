<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Rol;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');
        
        // Búsqueda por nombre o correo
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        
        // Filtro por rol
        if ($request->has('rol') && !empty($request->rol)) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('rol.idRol', $request->rol);
            });
        }
        
        $usuarios = $query->paginate(10);
        $roles = Rol::all();
        
        return view('Usuarios.usuario', compact('usuarios', 'roles'));
    }

    public function create()
    {
        $roles = Rol::all();
        return view('Usuarios.crearUsuario', compact('roles'));
    }

    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'apellidoPaterno' => 'required|string|max:255',
            'apellidoMaterno' => 'nullable|string|max:255',
            'ci' => 'required|integer|unique:users',
            'fechaNacimiento' => 'required|date',
            'genero' => 'required|in:M,F',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:rol,idRol',
            'email_verified_at' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Crear el usuario
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'apellidoPaterno' => $request->apellidoPaterno,
            'apellidoMaterno' => $request->apellidoMaterno,
            'ci' => $request->ci,
            'fechaNacimiento' => $request->fechaNacimiento,
            'genero' => $request->genero,
        ];
        
        // Si el checkbox de email verificado está marcado, establecer la fecha actual
        if ($request->has('email_verified_at')) {
            $userData['email_verified_at'] = now();
        }
        
        $usuario = User::create($userData);

        // Asignar roles al usuario
        foreach ($request->roles as $rolId) {
            DB::table('userRol')->insert([
                'id' => $usuario->id,
                'idRol' => $rolId,
                'habilitado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('usuarios')
            ->with('success', 'Usuario creado correctamente');
    }

    public function show($id)
    {
        $usuario = User::with('roles')->findOrFail($id);
        return view('Usuarios.verUsuario', compact('usuario'));
    }

    public function edit($id)
    {
        $usuario = User::with('roles')->findOrFail($id);
        $roles = Rol::all();
        $usuarioRoles = $usuario->roles->pluck('idRol')->toArray();
        
        return view('Usuarios.editarUsuario', compact('usuario', 'roles', 'usuarioRoles'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        
        // Validar los datos del formulario
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'apellidoPaterno' => 'required|string|max:255',
            'apellidoMaterno' => 'nullable|string|max:255',
            'ci' => 'required|integer|unique:users,ci,' . $id,
            'fechaNacimiento' => 'required|date',
            'genero' => 'required|in:M,F',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:rol,idRol',
        ];
        
        // Si se proporciona una contraseña, validarla
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Actualizar el usuario
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->apellidoPaterno = $request->apellidoPaterno;
        $usuario->apellidoMaterno = $request->apellidoMaterno;
        $usuario->ci = $request->ci;
        $usuario->fechaNacimiento = $request->fechaNacimiento;
        $usuario->genero = $request->genero;
        
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }
        
        $usuario->save();

        // Actualizar roles
        DB::table('userRol')->where('id', $usuario->id)->delete();
        
        foreach ($request->roles as $rolId) {
            DB::table('userRol')->insert([
                'id' => $usuario->id,
                'idRol' => $rolId,
                'habilitado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('usuarios')
            ->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        
        // Eliminar relaciones de roles
        DB::table('userRol')->where('id', $id)->delete();
        
        // Eliminar usuario
        $usuario->delete();

        return redirect()->route('usuarios')
            ->with('success', 'Usuario eliminado correctamente');
    }
}