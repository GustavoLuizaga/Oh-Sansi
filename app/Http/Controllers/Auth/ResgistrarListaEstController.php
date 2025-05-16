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
use Illuminate\Support\Facades\Log;
class ResgistrarListaEstController extends Controller
{
    public function index()
    {

        
    }

    public function validarDatosInscripcion(Request $request)
    {
        try {
            Log::info('Iniciando validación de datos de inscripción', [
                'request_data' => $request->all()
            ]);

            $tutor = Auth::user()->tutor;
            if (!$tutor) {
                Log::error('Usuario no tiene tutor asociado');
                return response()->json([
                    'valido' => false,
                    'mensaje' => 'Usuario no tiene permisos de tutor'
                ]);
            }

            $area = Area::where('nombre', $request->area)->first();
            if (!$area) {
                Log::error('Área no encontrada', ['area_nombre' => $request->area]);
                return response()->json([
                    'valido' => false,
                    'mensaje' => 'El área especificada no existe'
                ]);
            }

            $categoria = Categoria::where('nombre', $request->categoria)->first();
            if (!$categoria) {
                Log::error('Categoría no encontrada', ['categoria_nombre' => $request->categoria]);
                return response()->json([
                    'valido' => false,
                    'mensaje' => 'La categoría especificada no existe'
                ]);
            }

            $grado = Grado::where('grado', $request->grado)->first();
            if (!$grado) {
                Log::error('Grado no encontrado', ['grado' => $request->grado]);
                return response()->json([
                    'valido' => false,
                    'mensaje' => 'El grado especificado no existe'
                ]);
            }

            // Validar que el área pertenece al delegado
            $areaPertenece = $tutor->areas()
                ->where('area.idArea', $area->idArea)
                ->wherePivot('idConvocatoria', $request->idConvocatoria)
                ->exists();

            if (!$areaPertenece) {
                Log::error('Área no asignada al tutor', [
                    'tutor_id' => $tutor->id,
                    'area_id' => $area->idArea,
                    'convocatoria_id' => $request->idConvocatoria
                ]);
                return response()->json([
                    'valido' => false,
                    'mensaje' => 'El área seleccionada no está asignada al delegado en esta convocatoria'
                ]);
            }

            // Validar que la categoría corresponde al área
            $categoriaValida = $area->convocatoriaAreaCategorias()
                ->where('idCategoria', $categoria->idCategoria)
                ->where('idConvocatoria', $request->idConvocatoria)
                ->exists();

            if (!$categoriaValida) {
                Log::error('Categoría no válida para el área', [
                    'area_id' => $area->idArea,
                    'categoria_id' => $categoria->idCategoria,
                    'convocatoria_id' => $request->idConvocatoria
                ]);
                return response()->json([
                    'valido' => false,
                    'mensaje' => 'La categoría no corresponde al área seleccionada'
                ]);
            }

            // Validar que el grado corresponde a la categoría
            $gradoValido = $categoria->grados()
                ->where('grado.idGrado', $grado->idGrado)
                ->exists();

            if (!$gradoValido) {
                Log::error('Grado no válido para la categoría', [
                    'categoria_id' => $categoria->idCategoria,
                    'grado_id' => $grado->idGrado
                ]);
                return response()->json([
                    'valido' => false,
                    'mensaje' => 'El grado no corresponde a la categoría seleccionada'
                ]);
            }

            Log::info('Validación exitosa');
            return response()->json([
                'valido' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Error en validación de datos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'valido' => false,
                'mensaje' => 'Error al validar los datos: ' . $e->getMessage()
            ]);
        }
    }

    public function inscribirEstudiante($datos, $idConvocatoria)
    {
        try {
            DB::beginTransaction();

            // 1. Crear usuario
            $user = User::create([
                'name' => $datos['nombre'],
                'apellidoPaterno' => $datos['apellidoPaterno'],
                'apellidoMaterno' => $datos['apellidoMaterno'],
                'email' => $datos['email'] ?? $datos['ci'] . '@temp.com',
                'password' => Hash::make($datos['ci']),
                'ci' => $datos['ci'],
                'fechaNacimiento' => $datos['fechaNacimiento'],
                'genero' => $datos['genero']
            ]);

            // Asignar rol de estudiante (ID = 3)
            $user->roles()->attach(3);

            // 2. Crear estudiante
            $estudiante = Estudiante::create([
                'id' => $user->id
            ]);            // 3. Crear inscripción
            $tutor = Auth::user()->tutor;
            $idDelegacion = $tutor->primerIdDelegacion($idConvocatoria);            // Obtener el grado del estudiante
            $grado = Grado::where('grado', $datos['grado'])->first();
            if (!$grado) {
                throw new \Exception('El grado especificado no existe');
            }

            $inscripcion = Inscripcion::create([
                'fechaInscripcion' => now(),
                'status' => 'pendiente', // Cambiado a un valor válido según el enum definido
                'numeroContacto' => $datos['numeroContacto'] ?? 0, // Valor predeterminado si no está presente
                'idGrado' => $grado->idGrado,
                'idConvocatoria' => $idConvocatoria,
                'idDelegacion' => $idDelegacion,
                'nombreApellidosTutor' => Auth::user()->name . ' ' . Auth::user()->apellidoPaterno,
                'correoTutor' => Auth::user()->email
            ]);

            // 4. Relacionar tutor y estudiante con la inscripción
            TutorEstudianteInscripcion::create([
                'idEstudiante' => $estudiante->id,
                'idTutor' => $tutor->id,
                'idInscripcion' => $inscripcion->idInscripcion
            ]);

            // 5. Crear detalle de inscripción
            $area = Area::where('nombre', $datos['area'])->first();
            $categoria = Categoria::where('nombre', $datos['categoria'])->first();

            $detalleInscripcion = DetalleInscripcion::create([
                'modalidadInscripcion' => $datos['modalidad'] ?? 'individual',
                'idInscripcion' => $inscripcion->idInscripcion,
                'idArea' => $area->idArea,
                'idCategoria' => $categoria->idCategoria
            ]);

            // 6. Si hay grupo, relacionar con grupo
            if (isset($datos['codigoGrupo'])) {
                $grupo = GrupoInscripcion::where('codigoInvitacion', $datos['codigoGrupo'])
                    ->where('idDelegacion', $idDelegacion)
                    ->first();

                if ($grupo) {
                    $detalleInscripcion->update(['idGrupoInscripcion' => $grupo->id]);
                }
            }

            DB::commit();
            return true;        } catch (\Exception $e) {
            DB::rollBack();
            // Log the detailed error for debugging
            Log::error('Error en inscripción de estudiante', [
                'estudiante' => $datos['nombre'] . ' ' . $datos['apellidoPaterno'],
                'error' => $e->getMessage(),
                'datos' => $datos
            ]);
            
            // Re-throw with more descriptive message
            if (strpos($e->getMessage(), "Field 'numeroContacto' doesn't have a default value") !== false) {
                throw new \Exception('Falta el número de contacto para el estudiante ' . $datos['nombre'] . ' ' . $datos['apellidoPaterno']);
            } else if (strpos($e->getMessage(), "Field 'idGrado' doesn't have a default value") !== false) {
                throw new \Exception('Falta el grado para el estudiante ' . $datos['nombre'] . ' ' . $datos['apellidoPaterno']);
            } else {
                throw $e;
            }
        }
    }    public function store(Request $request)
    {
        try {
            // Validar la entrada
            if (!$request->has('estudiantes') || !is_array($request->input('estudiantes')) || empty($request->input('estudiantes'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se proporcionaron datos de estudiantes para inscribir',
                    'errores' => ['No se proporcionaron datos de estudiantes para inscribir']
                ]);
            }
            
            if (!$request->has('idConvocatoria')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se proporcionó el ID de la convocatoria',
                    'errores' => ['No se proporcionó el ID de la convocatoria']
                ]);
            }
            
            $estudiantes = $request->input('estudiantes');
            $idConvocatoria = $request->input('idConvocatoria');
            $errores = [];
            $inscritos = 0;

            foreach ($estudiantes as $index => $estudiante) {
                try {
                    // Validar datos mínimos del estudiante
                    if (!isset($estudiante['nombre']) || !isset($estudiante['apellidoPaterno']) || !isset($estudiante['ci'])) {
                        $errores[] = "Fila " . ($index + 1) . ": Faltan datos obligatorios del estudiante";
                        continue;
                    }
                    
                    $this->inscribirEstudiante($estudiante, $idConvocatoria);
                    $inscritos++;
                } catch (\Exception $e) {
                    Log::error("Error al inscribir estudiante", [
                        'estudiante' => $estudiante['nombre'] ?? 'desconocido',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $errores[] = "Error al inscribir a {$estudiante['nombre']} {$estudiante['apellidoPaterno']}: " . $e->getMessage();
                }
            }

            if (count($errores) > 0) {
                if ($inscritos > 0) {
                    return response()->json([
                        'success' => true,
                        'message' => "Se inscribieron {$inscritos} estudiante(s), pero hubo errores con " . count($errores) . " estudiante(s)",
                        'errores' => $errores
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se pudo inscribir a ningún estudiante',
                        'errores' => $errores
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Estudiantes inscritos correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error("Error general en inscripción", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar las inscripciones: ' . $e->getMessage(),
                'errores' => ['Error al procesar las inscripciones: ' . $e->getMessage()]
            ]);
        }
    }
}
