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
use App\Http\Controllers\Inscripcion\ObtenerAreasConvocatoria;
use App\Http\Controllers\Inscripcion\ObtenerCategoriasArea;
use App\Http\Controllers\Inscripcion\ObtenerGradosdeUnaCategoria;
use Illuminate\Support\Facades\DB;
use App\Models\TutorEstudianteInscripcion;

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
        $usersUpdated = 0;
        $errors = [];

        // si contiene encabezados omitimos la primera fila
        $rows = array_slice($array[0], 1);
        foreach ($rows as $key => $row) {
            if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3]) || empty($row[4]) || empty($row[5]) || empty($row[6])) {
                return back()->with('error_messages', [
                    "Error en fila " . ($key + 2) . ": Datos obligatorios faltantes. El proceso ha sido cancelado."
                ]);
            }
        }
        foreach ($rows as $key => $row) {
            DB::beginTransaction();
            try {
                // Verificar si el usuario ya existe por email
                $user = User::where('email', $row[4])->first();
                $isNewUser = false;
                if (!$user) {
                    // Crear nuevo usuario si no existe
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

                    $isNewUser = true;
                    $usersCreated++;
                }

                // Obtener IDs necesarios
                $convocatoria = new VerificarExistenciaConvocatoria();
                $idConvocatoriaResult = $convocatoria->verificarConvocatoriaActiva();

                // Verificar si el área está habilitada para la convocatoria
                $obtenerAreas = new ObtenerAreasConvocatoria();
                $areasHabilitadas = $obtenerAreas->obtenerAreasPorConvocatoria($idConvocatoriaResult);

                $area = $row[7];
                $areaExiste = $areasHabilitadas->contains('nombre', $area);

                if (!$areaExiste) {
                    throw new \Exception("El área '{$area}' no está habilitada para esta convocatoria. Por favor, póngase en contacto con el administrador del sistema.");
                }


                $idArea = Area::where('nombre', $area)->value('idArea');

                // Verificar si el estudiante ya está inscrito en este área
                $areasInscritas = TutorEstudianteInscripcion::where('idEstudiante', $user->id)
                    ->with('inscripcion.area')
                    ->get()
                    ->pluck('inscripcion.area.nombre')
                    ->filter()
                    ->unique()
                    ->values();

                if ($areasInscritas->contains($area)) {
                    throw new \Exception("El estudiante ya está inscrito en el área '{$area}'. No puede inscribirse dos veces en la misma área.");
                }


                $areaModel = Area::find($idArea);

                $categoriasArea = new ObtenerCategoriasArea();
                $categoriasHabilitadas = $categoriasArea->categoriasAreas($idArea);

                $categoria = $row[8];
                $categoriaExiste = $categoriasHabilitadas->contains('nombre', $categoria);
                if (!$categoriaExiste) {
                    throw new \Exception("La categoría '{$categoria}' no está habilitada para esta  Area '{$areaModel->nombre}' en esta convocatoria. Por favor, póngase en contacto con el administrador del sistema.");
                }
                $categoriaModel = Categoria::where('nombre', $categoria)->first();
                $idCategoria = $categoriaModel->idCategoria;



                $grado = $row[9];
                $gradosArea = new ObtenerGradosdeUnaCategoria();
                $gradosHabilitados = $gradosArea->obtenerGradosPorArea($categoriaModel);
                $gradoExiste = $gradosHabilitados->contains('grado', $grado);
                if (!$gradoExiste) {
                    throw new \Exception("El grado '{$row[9]}' no está habilitado para esta categoría de '{$categoriaModel->nombre}' en esta convocatoria. Por favor, póngase en contacto con el administrador del sistema.");
                }

                $idGrado = Grado::where('grado', $grado)->value('idGrado');

                $delegacion = $row[11];
                $delegacionExiste = Delegacion::where('nombre', $delegacion)->exists();
                if (!$delegacionExiste) {
                    throw new \Exception("La delegación '{$delegacion}' no existe. Por favor, póngase en contacto con el administrador del sistema.");
                }
                $idDelegacion = Delegacion::where('nombre', $delegacion)->value('idDelegacion');

                // Crear inscripción
                $inscripcion = Inscripcion::create([
                    'fechaInscripcion' => now(),
                    'numeroContacto' => $row[10],
                    'idConvocatoria' => $idConvocatoriaResult,
                    'idArea' => $idArea,
                    'idDelegacion' => $idDelegacion,
                    'idCategoria' => $idCategoria,
                    'idGrado' => $idGrado
                ]);

                // Asociar tutor y estudiante
                $inscripcion->tutores()->attach(Auth::user()->id, [
                    'idEstudiante' => $user->id
                ]);

                // Enviar notificación solo si es usuario nuevo
                if ($isNewUser) {
                    $user->notify(new WelcomeEmailNotification($plainPassword));
                    event(new Registered($user));
                } else {
                    $usersUpdated++;
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = "Error en fila " . ($key + 2) . ": " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            return back()
                ->with('error_messages', $errors)
                ->with('message', "Se crearon {$usersCreated} usuarios nuevos y se actualizaron {$usersUpdated} usuarios existentes");
        }

        return back()
            ->with('success', "Se crearon {$usersCreated} usuarios nuevos y se actualizaron {$usersUpdated} usuarios existentes");
    }
}
