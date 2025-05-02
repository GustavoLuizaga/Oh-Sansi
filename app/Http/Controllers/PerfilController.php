<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Rol;

class PerfilController extends Controller
{
    /**
     * Mostrar la vista del perfil del usuario autenticado
     */
    public function index()
    {
        $user = Auth::user();
        return view('perfil.perfil', compact('user'));
    }

    /**
     * Actualizar la información del perfil del usuario
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'apellidoPaterno' => 'nullable|string|max:255',
            'apellidoMaterno' => 'nullable|string|max:255',
            'ci' => 'nullable|integer',
            'fechaNacimiento' => 'nullable|date',
            'genero' => 'nullable|in:M,F,O',
        ]);

        $user->update($request->only([
            'name', 'email', 'apellidoPaterno', 'apellidoMaterno',
            'ci', 'fechaNacimiento', 'genero'
        ]));

        return redirect()->route('perfil.index')->with('success', 'Perfil actualizado correctamente');
    }

    /**
     * Actualizar la contraseña del usuario
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('perfil.index')->with('success', 'Contraseña actualizada correctamente');
    }
}