<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rol;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'apellidoPaterno' => $request->apellidoPaterno,
            'apellidoMaterno' => $request->apellidoMaterno,
            'ci' => $request->ci,
            'fechaNacimiento' => $request->fechaNacimiento,
            'genero' => $request->genero,
            'password' => Hash::make($request->password),
        ]);

        $rol = Rol::find(3);
        if ($rol) { 
            $user->roles()->attach($rol->idRol, ['habilitado' => true]);
            $user->estudiante()->create();

        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    //Para el registro de tutores//
    public function createTutor()
    {
    return view('auth.registerTutor'); 
      }

    public function storeTutor(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
  
        $user = User::create([
              'name' => $request->name,
              'email' => $request->email,
              'apellidoPaterno' => $request->apellidoPaterno,
              'apellidoMaterno' => $request->apellidoMaterno,
              'ci' => $request->ci,
              'fechaNacimiento' => $request->fechaNacimiento,
              'genero' => $request->genero,
              'password' => Hash::make($request->password),
          ]);
  
          $rol = Rol::find(2);
          if ($rol) { 
              $user->roles()->syncWithoutDetaching([$rol->idRol]);
              $user->tutor()->create([
                'profesion' => $request->profesion,
                'telefono' => $request->telefono,
                //aqui poner la loguica para el link de recurso
                'linkRecurso' => "link",
                //poner l alogica para que a un tutor le asigne una delegacion y area
            ]);
          }
  
          event(new Registered($user));
  
          Auth::login($user);
  
          return redirect(RouteServiceProvider::HOME);
      }
  

}
