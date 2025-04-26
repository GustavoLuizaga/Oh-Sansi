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

class ResgistrarListaEstController extends Controller
{
    public function index()
    {
        return view('inscripcion estudiante.RegistrarListaEst');
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