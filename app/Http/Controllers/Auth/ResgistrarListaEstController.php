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
use App\Models\DetalleInscripcion;
use App\Models\GrupoInscripcion;
use Illuminate\Support\Str;
use App\Events\CreacionCuenta;
use App\Events\InscripcionArea;

class ResgistrarListaEstController extends Controller
{
    public function index()
    {
        // Obtener información de áreas, categorías, etc. para mostrar en la vista
        $idConvocatoriaResult = (new VerificarExistenciaConvocatoria())->verificarConvocatoriaActiva();
        $areasHabilitadas = [];
        $delegaciones = Delegacion::all();

        if ($idConvocatoriaResult) {
            $areasHabilitadas = (new ObtenerAreasConvocatoria())->obtenerAreasPorConvocatoria($idConvocatoriaResult);
        }

        return view('inscripcion estudiante.RegistrarListaEst', [
            'convocatoriaActiva' => $idConvocatoriaResult ? true : false,
            'areas' => $areasHabilitadas,
            'delegaciones' => $delegaciones
        ]);
    }

    public function descargarPlantilla()
    {
        $generador = new GenerarPlantillaExcelController();
        $filePath = $generador->generarPlantilla();

        return response()->download($filePath, 'plantilla_inscripcion.xlsx');
    }    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'idConvocatoria' => 'required|exists:convocatoria,idConvocatoria'
        ]);

        // Usar la convocatoria seleccionada en el formulario
        $idConvocatoriaSeleccionada = $request->idConvocatoria;
        
        // Verificar que la convocatoria esté publicada
        $convocatoria = \App\Models\Convocatoria::where('idConvocatoria', $idConvocatoriaSeleccionada)
                         ->where('estado', 'Publicada')
                         ->first();
                         
        if (!$convocatoria) {
            return back()->with('error', 'La convocatoria seleccionada no está disponible o no está publicada.');
        }

        $array = Excel::toArray([], $request->file('file'));
        $rows = array_slice($array[0], 1); // Omitir la fila de encabezados

        $errors = [];
        $usersCreated = 0;
        $usersUpdated = 0;

        $filaDatos = []; // Aquí vamos a guardar las filas validadas y procesables
        $gruposInvitacion = []; // Para agrupar estudiantes por código de invitación
        $estudiantesPorCI = []; // Para agrupar estudiantes por CI y validar el límite de áreas

        // PRIMERA PASADA: Validar datos sin tocar la base de datos
        foreach ($rows as $key => $row) {
            $currentRow = $key + 2; // +2 porque Excel empieza en 1 y ya omitimos la fila de encabezados

            if (empty($row[4])) { // Validar si falta email
                $errors[] = "Fila {$currentRow}: El correo electrónico es obligatorio.";
                continue;
            }

            $area = $row[7] ?? null;
            $categoria = $row[8] ?? null;
            $grado = $row[9] ?? null;
            $delegacion = $row[11] ?? null;
            $modalidad = $row[14] ?? 'Individual';
            $codigoInvitacion = $row[15] ?? null;

            // Validar modalidad y código de invitación
            if (in_array(strtolower($modalidad), ['duo', 'equipo']) && empty($codigoInvitacion)) {
                $errors[] = "Fila {$currentRow}: Para modalidad '{$modalidad}' debe proporcionar un código de invitación.";
                continue;
            }

            // Usar el ID de convocatoria seleccionado en el formulario
            $idConvocatoriaResult = $idConvocatoriaSeleccionada;
            $areasHabilitadas = (new ObtenerAreasConvocatoria())->obtenerAreasPorConvocatoria($idConvocatoriaResult);

            if (!$areasHabilitadas->contains('nombre', $area)) {
                $errors[] = "Fila {$currentRow}: El área '{$area}' no está habilitada para esta convocatoria. Por favor revise la información de la convocatoria vigente.";
                continue;
            }

            // Agrupar estudiantes por CI para validar el límite de áreas
            $ci = $row[3] ?? null;
            if (empty($ci)) {
                $errors[] = "Fila {$currentRow}: El CI es obligatorio.";
                continue;
            }

            if (!isset($estudiantesPorCI[$ci])) {
                $estudiantesPorCI[$ci] = [
                    'areas' => [],
                    'filas' => []
                ];
            }

            // Verificar que no se repita la misma área para el mismo estudiante
            if (in_array($area, $estudiantesPorCI[$ci]['areas'])) {
                $errors[] = "Fila {$currentRow}: El estudiante con CI '{$ci}' ya tiene una inscripción en el área '{$area}' en este archivo.";
                continue;
            }

            // Agregar el área a la lista de áreas del estudiante
            $estudiantesPorCI[$ci]['areas'][] = $area;
            $estudiantesPorCI[$ci]['filas'][] = $currentRow;

            // Validar que el área pertenezca al tutor actual
            $tutorId = Auth::user()->id;
            $tutorAreas = DB::table('tutorAreaDelegacion')
                ->join('area', 'tutorAreaDelegacion.idArea', '=', 'area.idArea')
                ->where('tutorAreaDelegacion.id', $tutorId)
                ->pluck('area.nombre')
                ->toArray();

            if (!in_array($area, $tutorAreas)) {
                $errors[] = "Fila {$currentRow}: El área '{$area}' no está asignada al tutor actual. Solo puede inscribir estudiantes en sus áreas asignadas.";
                continue;
            }

            $idArea = Area::where('nombre', $area)->value('idArea');

            // Validar existencia de categoría en el área
            $categoriasHabilitadas = (new ObtenerCategoriasArea())->categoriasAreas2($idConvocatoriaResult, $idArea);
            if (!$categoriasHabilitadas->contains('nombre', $categoria)) {
                $errors[] = "Fila {$currentRow}: La categoría '{$categoria}' no está habilitada para el área '{$area}'. Por favor revise la información de la convocatoria vigente.";
                continue;
            }

            $categoriaModel = Categoria::where('nombre', $categoria)->first();

            // Validar existencia de grado en la categoría
            $gradosHabilitados = (new ObtenerGradosdeUnaCategoria())->obtenerGradosPorArea($categoriaModel);
            if (!$gradosHabilitados->contains('grado', $grado)) {
                $errors[] = "Fila {$currentRow}: El grado '{$grado}' no está habilitado para la categoría '{$categoriaModel->nombre}'. Por favor revise la información de la convocatoria vigente.";
                continue;
            }

            // Validar existencia de delegación
            if (!Delegacion::where('nombre', $delegacion)->exists()) {
                $errors[] = "Fila {$currentRow}: La delegación '{$delegacion}' no existe.";
                continue;
            }

            $tutorId = Auth::user()->id;
            $tutorDelegaciones = DB::table('tutorAreaDelegacion')
                ->join('delegacion', 'tutorAreaDelegacion.idDelegacion', '=', 'delegacion.idDelegacion')
                ->where('tutorAreaDelegacion.id', $tutorId)
                ->pluck('delegacion.nombre')
                ->toArray();

            if (!in_array($delegacion, $tutorDelegaciones)) {
                $errors[] = "Fila {$currentRow}: La delegación '{$delegacion}' no está asignada al tutor actual. Solo puede inscribir estudiantes en sus delegaciones asignadas.";
                continue;
            }

            // Guardar información para grupos de invitación
            if (!empty($codigoInvitacion)) {
                if (!isset($gruposInvitacion[$codigoInvitacion])) {
                    $gruposInvitacion[$codigoInvitacion] = [
                        'modalidad' => $modalidad,
                        'area' => $area,
                        'categoria' => $categoria,
                        'miembros' => []
                    ];
                }

                $gruposInvitacion[$codigoInvitacion]['miembros'][] = $currentRow;

                // Validar que todos los miembros del grupo tengan la misma área y categoría
                if (
                    $gruposInvitacion[$codigoInvitacion]['area'] != $area ||
                    $gruposInvitacion[$codigoInvitacion]['categoria'] != $categoria
                ) {
                    $errors[] = "Fila {$currentRow}: Los estudiantes con el mismo código de invitación deben inscribirse en la misma área y categoría.";
                    continue;
                }
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
                'modalidad' => $modalidad,
                'codigoInvitacion' => $codigoInvitacion
            ];
        }

        // Validar grupos de invitación
        foreach ($gruposInvitacion as $codigo => $grupo) {
            $modalidad = strtolower($grupo['modalidad']);
            $cantidadMiembros = count($grupo['miembros']);

            // Validar número de miembros según modalidad
            if ($modalidad == 'duo') {
                if ($cantidadMiembros != 2) {
                    $errors[] = "Código de invitación '{$codigo}': La modalidad Dúo requiere exactamente 2 estudiantes (actualmente tiene {$cantidadMiembros}).";
                }
            } elseif ($modalidad == 'equipo') {
                if ($cantidadMiembros < 3) {
                    $errors[] = "Código de invitación '{$codigo}': La modalidad Equipo requiere al menos 3 estudiantes (actualmente tiene {$cantidadMiembros}).";
                } elseif ($cantidadMiembros > 10) {
                    $errors[] = "Código de invitación '{$codigo}': La modalidad Equipo permite máximo 10 estudiantes (actualmente tiene {$cantidadMiembros}).";
                }
            }
        }

        // Validar que ningún estudiante se inscriba en más de 2 áreas
        foreach ($estudiantesPorCI as $ci => $datos) {
            if (count($datos['areas']) > 2) {
                $filasStr = implode(', ', $datos['filas']);
                $areasStr = implode(', ', $datos['areas']);
                $errors[] = "El estudiante con CI '{$ci}' (filas {$filasStr}) está intentando inscribirse en más de 2 áreas: {$areasStr}. Solo se permiten máximo 2 áreas por estudiante.";
            }
        }

        // SI HUBO ERRORES DE VALIDACIÓN, no hacemos nada
        if (!empty($errors)) {
            return back()->with('error_messages', $errors);
        }

        // SEGUNDA PASADA: Guardar datos ahora sí en base de datos
        DB::beginTransaction();

        try {
            // Crear un array para almacenar los grupos creados
            $gruposCreados = [];

            foreach ($filaDatos as $data) {
                $row = $data['row'];
                $currentRow = $data['currentRow'];

                // Buscar usuario
                $user = User::where('email', $row[4])->first();
                $isNewUser = false;

                if (!$user) {
                    $plainPassword = $row[3]; // Usar CI como contraseña inicial

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

                    event(new CreacionCuenta(
                        $user->id,
                        '¡Tu cuenta ha sido creada exitosamente!, Por seguridad te recoendamos cambiar tu contraseña.',
                        'sistema'
                    ));

                    $rol = Rol::find(3); // Rol de estudiante
                    if ($rol) {
                        $user->roles()->attach($rol->idRol, ['habilitado' => true]);
                        $user->estudiante()->create();
                    }

                    $isNewUser = true;
                    $usersCreated++;
                } else {
                    $usersUpdated++;
                }

                // Validar que no esté ya inscrito en el área y que no exceda el límite de 2 áreas
                $areasInscritas = DetalleInscripcion::join('inscripcion', 'detalle_inscripcion.idInscripcion', '=', 'inscripcion.idInscripcion')
                    ->join('tutorEstudianteInscripcion', 'inscripcion.idInscripcion', '=', 'tutorEstudianteInscripcion.idInscripcion')
                    ->join('area', 'detalle_inscripcion.idArea', '=', 'area.idArea')
                    ->where('tutorEstudianteInscripcion.idEstudiante', $user->id)
                    ->where('inscripcion.idConvocatoria', $data['idConvocatoriaResult'])
                    ->pluck('area.nombre')
                    ->unique()
                    ->values();

                // Verificar si ya está inscrito en esta área específica
                if ($areasInscritas->contains($row[7])) {
                    $errors[] = "Fila {$currentRow}: El estudiante ya está inscrito en el área '{$row[7]}'.";
                    continue;
                }

                // Verificar si ya alcanzó el límite de 2 áreas en la base de datos
                if ($areasInscritas->count() >= 2) {
                    $areasInscritasStr = $areasInscritas->implode(', ');
                    $errors[] = "Fila {$currentRow}: El estudiante ya está inscrito en 2 áreas ({$areasInscritasStr}) y no puede inscribirse en más áreas.";
                    continue;
                }

                // Buscar si el estudiante ya tiene una inscripción en la convocatoria actual
                $inscripcionExistente = Inscripcion::join('tutorEstudianteInscripcion', 'inscripcion.idInscripcion', '=', 'tutorEstudianteInscripcion.idInscripcion')
                    ->where('tutorEstudianteInscripcion.idEstudiante', $user->id)
                    ->where('inscripcion.idConvocatoria', $data['idConvocatoriaResult'])
                    ->first();

                if ($inscripcionExistente) {
                    // Si ya tiene una inscripción, usamos esa
                    $inscripcion = Inscripcion::find($inscripcionExistente->idInscripcion);
                } else {
                    // Si no tiene inscripción, creamos una nueva
                    $inscripcion = Inscripcion::create([
                        'fechaInscripcion' => now(),
                        'numeroContacto' => $row[10],
                        'idConvocatoria' => $data['idConvocatoriaResult'],
                        'idDelegacion' => $data['idDelegacion'],
                        'idGrado' => $data['idGrado'],
                        'nombreApellidosTutor' => $row[12],
                        'correoTutor' => $row[13],
                        'status' => 'pendiente'
                    ]);

                    // Asociar estudiante con tutor e inscripción
                    $inscripcion->tutores()->attach(Auth::user()->id, [
                        'idEstudiante' => $user->id,
                    ]);

                    event(new InscripcionArea(
                        $user->id,
                        'Te has inscrito exitosamente en el área: ' . $row[7] . '.',
                        'sistema'
                    ));
                }

                // Crear detalle de inscripción
                $modalidadInscripcion = strtolower($data['modalidad']);
                $idGrupoInscripcion = null;

                // Si tiene código de invitación, asociar al grupo
                if (!empty($data['codigoInvitacion'])) {
                    $codigoInvitacion = $data['codigoInvitacion'];

                    // Crear grupo si no existe
                    if (!isset($gruposCreados[$codigoInvitacion])) {
                        // Verificar si ya existe un grupo con este código de invitación en la base de datos
                        $grupoExistente = GrupoInscripcion::where('codigoInvitacion', $codigoInvitacion)->first();

                        if ($grupoExistente) {
                            // Si el grupo ya existe, usar ese grupo
                            $gruposCreados[$codigoInvitacion] = $grupoExistente->id;
                        } else {
                            // Si no existe, crear un nuevo grupo
                            $grupoInscripcion = GrupoInscripcion::create([
                                'codigoInvitacion' => $codigoInvitacion,
                                'nombreGrupo' => 'Grupo ' . $codigoInvitacion,
                                'modalidad' => $modalidadInscripcion,
                                'estado' => 'incompleto',
                                'idDelegacion' => $data['idDelegacion']
                            ]);

                            $gruposCreados[$codigoInvitacion] = $grupoInscripcion->id;
                        }
                    }

                    $idGrupoInscripcion = $gruposCreados[$codigoInvitacion];
                }

                // Crear el detalle de inscripción
                $detalleInscripcion = DetalleInscripcion::create([
                    'modalidadInscripcion' => $modalidadInscripcion,
                    'idInscripcion' => $inscripcion->idInscripcion,
                    'idArea' => $data['idArea'],
                    'idCategoria' => $data['idCategoria'],
                    'idGrupoInscripcion' => $idGrupoInscripcion
                ]);

                // Notificar si es nuevo usuario
                if ($isNewUser) {
                    $user->notify(new WelcomeEmailNotification($plainPassword));
                    event(new Registered($user));
                }
                // Notificar al estudiante sobre la inscripción
            }

            // Si hubo errores de inscripción (como áreas ya inscritas), también rollback
            if (!empty($errors)) {
                DB::rollBack();
                return back()->with('error_messages', $errors);
            }

            // Actualizar el estado de los grupos según la cantidad de miembros
            foreach ($gruposCreados as $codigoInvitacion => $grupoId) {
                $grupo = GrupoInscripcion::find($grupoId);
                $cantidadMiembros = DetalleInscripcion::where('idGrupoInscripcion', $grupoId)->count();

                // Verificar si el grupo cumple con los requisitos de miembros según su modalidad
                if ($grupo->modalidad == 'duo' && $cantidadMiembros == 2) {
                    $grupo->update(['estado' => 'activo']);
                } elseif ($grupo->modalidad == 'equipo' && $cantidadMiembros >= 3 && $cantidadMiembros <= 10) {
                    $grupo->update(['estado' => 'activo']);
                }
            }

            DB::commit();

            $mensaje = "¡Inscripción completada con éxito! ";

            if ($usersCreated > 0 && $usersUpdated > 0) {
                $mensaje .= "Se crearon {$usersCreated} usuarios nuevos y se actualizaron {$usersUpdated} usuarios existentes.";
            } elseif ($usersCreated > 0) {
                $mensaje .= "Se crearon {$usersCreated} usuarios nuevos.";
            } elseif ($usersUpdated > 0) {
                $mensaje .= "Se actualizaron {$usersUpdated} usuarios existentes.";
            }

            return back()->with('success', $mensaje);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error_messages', [$e->getMessage()]);
        }
    }
}
