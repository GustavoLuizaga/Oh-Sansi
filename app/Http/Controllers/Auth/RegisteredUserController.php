<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rol;
use App\Models\Delegacion;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use App\Http\Controllers\Inscripcion\ObtenerAreasConvocatoria;
use App\Http\Controllers\Inscripcion\VerificarExistenciaConvocatoria;


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

        $unidades = Delegacion::all();
        //Logica para obtener las Areas habilitadas de la base de datos
        $convocatoria = new VerificarExistenciaConvocatoria();
        $idConvocatoria = $convocatoria->verificarConvocatoriaActiva(); //Esto solo un ID
        if ($idConvocatoria instanceof \Illuminate\Http\JsonResponse) {
            return $idConvocatoria; // Retorna la respuesta JSON si no hay convocatoria activa
        }
        //Obtener las areas por el id de la convocatoria
        $obtenerAreas = new ObtenerAreasConvocatoria();
        $areas = $obtenerAreas->obtenerAreasPorConvocatoria($idConvocatoria); //Una lista de areas
        if ($areas instanceof \Illuminate\Http\JsonResponse) {
            return $areas; // Retorna la respuesta JSON si no se obtienen áreas
        }

        return view('auth.registerTutor', compact('unidades', 'areas'));
    }

    public function storeTutor(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'apellidoPaterno' => ['required', 'string', 'max:255'],
            'apellidoMaterno' => ['required', 'string', 'max:255'],
            'ci' => ['required', 'numeric', 'min:7'],  // Asegura que el CI sea un número con al menos 7 dígitos
            'fechaNacimiento' => ['required', 'date'],
            'genero' => ['required', 'in:M,F'],  // Validación para "Masculino" y "Femenino"
            'telefono' => ['required', 'numeric', 'min:8'],  // Teléfono con mínimo 8 dígitos
            'profesion' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],  // Validación del email y su unicidad
            'delegacion_tutoria' => ['required', 'exists:delegacion,idDelegacion'],  // Validación de que la delegación existe
            'area_tutoria' => ['required', 'exists:area,idArea'],  // Validación de que el área existe
            'password' => ['required', 'confirmed', Rules\Password::defaults()],  // Validación de la contraseña
            'cv' => ['required', 'mimes:pdf', 'max:2048'],  // Validación del archivo PDF
            'terms' => ['required', 'accepted'],  // Validación para aceptar los términos y condiciones
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
            $fileUrl = null;
            if ($request->hasFile('cv')) {
                $path = $request->file('cv')->store('public/cvs');
                $fileUrl = asset('storage/' . str_replace('public/', '', $path));
            }
            $tutor = $user->tutor()->create([
                'profesion' => $request->profesion,
                'telefono' => $request->telefono,
                //aqui poner la loguica para el link de recurso
                'linkRecurso' => $fileUrl,

            ]);
            $tutor->areas()->attach($request->area_tutoria, [
                'idDelegacion' => $request->delegacion_tutoria,
                'tokenTutor' => Str::random(20) // o genera como prefieras
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }



    public function storeDelegadoDelegacion(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'apellidoPaterno' => ['required', 'string', 'max:255'],
            'apellidoMaterno' => ['required', 'string', 'max:255'],
            'ci' => ['required', 'numeric', 'min:7'],  // Asegura que el CI sea un número con al menos 7 dígitos
            'fechaNacimiento' => ['required', 'date'],
            'genero' => ['required', 'in:M,F'],  // Validación para "Masculino" y "Femenino"
            'telefono' => ['required', 'numeric', 'min:8'],  // Teléfono con mínimo 8 dígitos
            'profesion' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],  // Validación del email y su unicidad
            'delegacion_tutoria' => ['required', 'exists:delegacion,idDelegacion'],  // Validación de que la delegación existe
            'area_tutoria' => ['required', 'exists:area,idArea'],  // Validación de que el área existe
            'password' => ['required', 'confirmed', Rules\Password::defaults()],  // Validación de la contraseña
            'cv' => ['required', 'mimes:pdf', 'max:2048'],  // Validación del archivo PDF
            'terms' => ['required', 'accepted'],  // Validación para aceptar los términos y condiciones
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
            $fileUrl = null;
            if ($request->hasFile('cv')) {
                $path = $request->file('cv')->store('public/cvs');
                $fileUrl = asset('storage/' . str_replace('public/', '', $path));
            }
            $tutor = $user->tutor()->create([
                'profesion' => $request->profesion,
                'telefono' => $request->telefono,
                //aqui poner la loguica para el link de recurso
                'linkRecurso' => $fileUrl,

            ]);
            $tutor->areas()->attach($request->area_tutoria, [
                'idDelegacion' => $request->delegacion_tutoria,
                'tokenTutor' => Str::random(20) // o genera como prefieras
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
