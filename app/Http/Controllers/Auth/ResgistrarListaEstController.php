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
use App\Models\Tutor;

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
        $rows = array_slice($array[0], 1);

        $errors = [];
        $usersCreated = 0;
        $usersUpdated = 0;

        $filaDatos = []; // Aquí vamos a guardar las filas validadas y procesables

        // PRIMERA PASADA: Validar datos sin tocar la base de datos
        foreach ($rows as $key => $row) {
            $currentRow = $key + 2;

            if (empty($row[4])) { // Validar si falta email
                $errors[] = "Fila {$currentRow}: El correo electrónico es obligatorio.";
                continue;
            }

            $area = $row[7] ?? null;
            $categoria = $row[8] ?? null;
            $grado = $row[9] ?? null;
            $delegacion = $row[11] ?? null;

            // Validar existencia de área
            $idConvocatoriaResult = (new VerificarExistenciaConvocatoria())->verificarConvocatoriaActiva();
            $areasHabilitadas = (new ObtenerAreasConvocatoria())->obtenerAreasPorConvocatoria($idConvocatoriaResult);

            if (!$areasHabilitadas->contains('nombre', $area)) {
                $errors[] = "Fila {$currentRow}: El área '{$area}' no está habilitada para esta convocatoria.Porfavor revise la información de la convocatoria vigente";
                continue;
            }

            $idArea = Area::where('nombre', $area)->value('idArea');

            // Validar existencia de categoría en el área
            $categoriasHabilitadas = (new ObtenerCategoriasArea())->categoriasAreas2($idConvocatoriaResult, $idArea);
            if (!$categoriasHabilitadas->contains('nombre', $categoria)) {
                $errors[] = "Fila {$currentRow}: La categoría '{$categoria}' no está habilitada para el área '{$area}'.Porfavor revise la información de la convocatoria vigente";
                continue;
            }

            $categoriaModel = Categoria::where('nombre', $categoria)->first();

            // Validar existencia de grado en la categoría
            $gradosHabilitados = (new ObtenerGradosdeUnaCategoria())->obtenerGradosPorArea($categoriaModel);
            if (!$gradosHabilitados->contains('grado', $grado)) {
                $errors[] = "Fila {$currentRow}: El grado '{$grado}' no está habilitado para la categoría '{$categoriaModel->nombre}'.Porfavor revise la información de la convocatoria vigente";
                continue;
            }

            // Validar existencia de delegación
            if (!Delegacion::where('nombre', $delegacion)->exists()) {
                $errors[] = "Fila {$currentRow}: La delegación '{$delegacion}' no existe.";
                continue;
            }

            $tutor = Tutor::find(Auth::user()->id);
            $areasSimples = $tutor->areasSimple;

            if(!$areasSimples->contains('nombre', $area)) {
                $errors[] = "Fila {$currentRow}: El área '{$area}' no esta asigada. Porfavor revise las areas a las que esta asigando";
                continue;
            }


            // Guardamos la fila que sí pasó todas las validaciones
            $filaDatos[] = [
                'row' => $row,
                'currentRow' => $currentRow,
                'idArea' => $idArea,
                'idCategoria' => $categoriaModel->idCategoria,
                'idGrado' => Grado::where('grado', $grado)->value('idGrado'),
                'idDelegacion' => Delegacion::where('nombre', $delegacion)->value('idDelegacion'),
                'idConvocatoriaResult' => $idConvocatoriaResult,
            ];
        }

        // SI HUBO ERRORES DE VALIDACIÓN, no hacemos nada
        if (!empty($errors)) {
            return back()->with('error_messages', $errors);
        }

        // SEGUNDA PASADA: Guardar datos ahora sí en base de datos
        DB::beginTransaction();

        try {
            foreach ($filaDatos as $data) {
                $row = $data['row'];
                $currentRow = $data['currentRow'];

                // Buscar usuario
                $user = User::where('email', $row[4])->first();
                $isNewUser = false;

                if (!$user) {
                    $plainPassword = $row[3];

                    $user = User::create([
                        'name' => $row[0],
                        'apellidoPaterno' => $row[1],
                        'apellidoMaterno' => $row[2],
                        'ci' => $row[3],
                        'email' => $row[4],
                        'fechaNacimiento' => is_numeric($row[5])
                            ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[5])->format('Y-m-d')
                            : \Carbon\Carbon::parse($row[5])->format('Y-m-d'),
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
                } else {
                    $usersUpdated++;
                }

                // Validar que no esté ya inscrito en el área
                $areasInscritas = TutorEstudianteInscripcion::where('idEstudiante', $user->id)
                    ->with('inscripcion.area')
                    ->get()
                    ->pluck('inscripcion.area.nombre')
                    ->filter()
                    ->unique()
                    ->values();

                if ($areasInscritas->contains($row[7])) {
                    $errors[] = "Fila {$currentRow}: El estudiante ya está inscrito en el área '{$row[7]}'.";
                    continue;
                }

                $inscripcionModel = new TutorEstudianteInscripcion();

                // Obtener cantidad de inscripciones

                $cantidadAreasInscritas = $inscripcionModel->cantidadAreasInscritas($user->id, $idConvocatoriaResult);

                if ($cantidadAreasInscritas >= 2) {
                    $errors[] = "Fila {$currentRow}: El estudiante ya está inscrito en el máximo de 2 áreas permitidas. Porfavor revise la información de la inscripcion.";
                    continue;
                }

                // Crear inscripción
                $inscripcion = Inscripcion::create([
                    'fechaInscripcion' => now(),
                    'numeroContacto' => $row[10],
                    'idConvocatoria' => $data['idConvocatoriaResult'],
                    'idArea' => $data['idArea'],
                    'idDelegacion' => $data['idDelegacion'],
                    'idCategoria' => $data['idCategoria'],
                    'idGrado' => $data['idGrado'],
                    'nombreApellidosTutor' => $row[12],
                    'correoTutor' => $row[13],
                ]);

                $inscripcion->tutores()->attach(Auth::user()->id, [
                    'idEstudiante' => $user->id,
                ]);

                // Notificar si es nuevo usuario
                if ($isNewUser) {
                    $user->notify(new WelcomeEmailNotification($plainPassword));
                    event(new Registered($user));
                }
            }

            // Si hubo errores de inscripción (como áreas ya inscritas), también rollback
            if (!empty($errors)) {
                DB::rollBack();
                return back()->with('error_messages', $errors);
            }

            DB::commit();
            return back()->with('success', "Se crearon {$usersCreated} usuarios nuevos y se inscribieron a {$usersUpdated} usuarios existentes.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error_messages', [$e->getMessage()]);
        }
    }
}
