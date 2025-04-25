<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Notifications\WelcomeEmailNotification;
use App\Models\Area;
use App\Models\Delegacion;
use App\Models\Estudiante;
use App\Models\Inscripcion;
use App\Models\Categoria;
use App\Models\Grado;
use App\Http\Controllers\Inscripcion\VerificarExistenciaConvocatoria;
use Illuminate\Support\Facades\Auth;

class ResgistrarListaEstController extends Controller
{
    public function index()
    {
        return view('RegistrarListaEst');
    }
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $array = Excel::toArray([], $request->file('file'));
        $usersCreated = 0;
        $errors = [];

        // si contiene encabezados omitimos la primera fila
        $rows = array_slice($array[0], 1);

        foreach ($rows as $key => $row) {
            try {
                $plainPassword = $row[3];
                $user = User::create([
                    'name' => $row[0], 
                    'apellidoPaterno' => $row[1], 
                    'apellidoMaterno' => $row[2], 
                    'ci' => $row[3], 
                    'email' => $row[4], 
                    'fechaNacimiento' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[5])->format('Y-m-d'), 
                    'genero' => $row[6], 
                    'password' => Hash::make($row[3]), 
                ]);

                $rol = Rol::find(3);
                if ($rol) {
                    $user->roles()->attach($rol->idRol, ['habilitado' => true]);
                    $user->estudiante()->create();
                }

                $area = $row[7];
                $idArea = Area::where('nombre', $area)->value('idArea');

                $categoria = $row[8];
                $idCategoria = Categoria::where('nombre', $categoria)->value('idCategoria');
                
                $grado = $row[9];
                $idGrado = Grado::where('grado', $grado)->value('idGrado');

                $delegacion = $row[11];
                $idDelegacion = Delegacion::where('nombre', $delegacion)->value('idDelegacion');

                $convocatoria = new VerificarExistenciaConvocatoria();
                $idConvocatoriaResult = $convocatoria->verificarConvocatoriaActiva();



                $inscripcion = Inscripcion::create([
                    'fechaInscripcion' => now(),
                    'numeroContacto' => $row[10],
                    'idConvocatoria' => $idConvocatoriaResult,
                    'idArea' => $idArea,
                    'idDelegacion' => $idDelegacion,
                    'idCategoria' => $idCategoria, 
                    'idGrado' => $idGrado
                ]);

                $inscripcion->tutores()->attach(Auth::user()->id, [
                    'idEstudiante' => $user->id
                ]);

                $user->notify(new WelcomeEmailNotification($plainPassword));
                
                event(new Registered($user));
                $usersCreated++;

            } catch (\Exception $e) {
                $errors[] = "Error en fila " . ($key+ 2) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'message' => "Se crearon $usersCreated usuarios exitosamente",
            'errors' => $errors
        ]);
    }
}