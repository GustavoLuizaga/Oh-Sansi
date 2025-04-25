<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DefaultDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {/*
        // Crear roles si no existen
        $roles = ['Administrador', 'Tutor', 'Estudiante'];

        foreach ($roles as $rol) {
            DB::table('rol')->updateOrInsert(
                ['nombre' => $rol]
            );
        }

        // Obtener ID del rol administrador
        $rolAdmin = DB::table('rol')->where('nombre', 'Administrador')->first();
        $rolTutor = DB::table('rol')->where('nombre', 'Tutor')->first();
        $rolEstudiante = DB::table('rol')->where('nombre', 'Estudiante')->first();
        // Verificar si ya hay un usuario con rol administrador
        $adminYaExiste = DB::table('userRol')->where('idRol', $rolAdmin->idRol)->exists();

        if (!$adminYaExiste) {
            // Crear usuario administrador
            $adminId = DB::table('users')->insertGetId([
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345678'), // Puedes cambiar la contraseña
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Asignar el rol administrador
            DB::table('userRol')->insert([
                'id' => $adminId,
                'idRol' => $rolAdmin->idRol,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // Crear usuario Estudiante si no existe
        $estudianteYaExiste = DB::table('userRol')->where('idRol', $rolEstudiante->idRol)->exists();

        if (!$estudianteYaExiste) {
            // Crear usuario estudiante
            $estudianteId = DB::table('users')->insertGetId([
                'name' => 'Estudiante',
                'email' => 'estudiante@gmail.com',
                'password' => Hash::make('12345678'), // Puedes cambiar la contraseña
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Asignar el rol estudiante
            DB::table('userRol')->insert([
                'id' => $estudianteId,
                'idRol' => $rolEstudiante->idRol,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // Crear usuario Tutor si no existe
        $tutorYaExiste = DB::table('userRol')->where('idRol', $rolTutor->idRol)->exists();

        if (!$tutorYaExiste) {
            // Crear usuario tutor
            $tutorId = DB::table('users')->insertGetId([
                'name' => 'Tutor',
                'email' => 'tutor@gmail.com',
                'password' => Hash::make('12345678'), // Puedes cambiar la contraseña
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // Verificar si es el primer tutor y asignar habilitado = true
            $habilitado = DB::table('userRol')->where('idRol', $rolTutor->idRol)->count() == 0 ? true : false;
            // Asignar el rol tutor
            DB::table('userRol')->insert([
                'id' => $tutorId,
                'idRol' => $rolTutor->idRol,
                'habilitado' => $habilitado,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Insertar funciones
        $funciones = ['Dashboard', 'Notificaciones', 'Delegaciones', 'Convocatoria', 'Registro', 'AreasCategorias', 'Perfil', 'Seguridad', 'InscripcionEstudiante', 'InscripcionTutor'];
        foreach ($funciones as $funcion) {
            DB::table('funcion')->updateOrInsert(['nombre' => $funcion]);
        }

        // Insertar interfaces de usuario (IU)
        $ius = ['Dashboard', 'Notificaciones', 'Delegaciones', 'Convocatoria', 'Registro', 'AreaCategoria', 'Perfil', 'Seguridad', 'InscripcionEstudiante', 'InscripcionTutor'];
        foreach ($ius as $iu) {
            DB::table('iu')->updateOrInsert(['nombreIu' => $iu]);
        }
        // Obtener roles
        $rolAdminId = DB::table('rol')->where('nombre', 'administrador')->value('idRol');
        $rolEstudianteId = DB::table('rol')->where('nombre', 'estudiante')->value('idRol');
        $rolTutorId = DB::table('rol')->where('nombre', 'Tutor')->value('idRol');
        // Obtener funciones
        $funcionesAll = DB::table('funcion')->get();

        // Relacionar Administrador con funciones específicas
        $funcionesAdmin = DB::table('funcion')
            ->whereIn('nombre', [
                'Dashboard',
                'Delegaciones',
                'Convocatoria',
                'AreasCategorias',
                'Seguridad'
            ])
            ->get();

        foreach ($funcionesAdmin as $funcion) {
            DB::table('rolFuncion')->updateOrInsert([
                'idFuncion' => $funcion->idFuncion,
                'idRol' => $rolAdminId
            ]);
        }

        // Relacionar Estudiante con Dashboard e InscripcionEstudiante
        $funcionesEstudiante = DB::table('funcion')
            ->whereIn('nombre', ['Dashboard', 'InscripcionEstudiante'])
            ->get();

        foreach ($funcionesEstudiante as $funcion) {
            DB::table('rolFuncion')->updateOrInsert([
                'idFuncion' => $funcion->idFuncion,
                'idRol' => $rolEstudianteId
            ]);
        }

        // Relacionar Tutor con Dashboard e InscripcionTutor
        $funcionesTutor = DB::table('funcion')
            ->whereIn('nombre', ['Dashboard', 'InscripcionTutor'])
            ->get();

        foreach ($funcionesTutor as $funcion) {
            DB::table('rolFuncion')->updateOrInsert([
                'idFuncion' => $funcion->idFuncion,
                'idRol' => $rolTutorId
            ]);
        }
        // Relacionar cada funcion con su interfaz de usuario equivalente
        foreach ($funcionesAll as $funcion) {
            // Buscar la IU con el mismo nombre
            $iu = DB::table('iu')->where('nombreIu', $funcion->nombre)->first();

            if ($iu) {
                DB::table('funcionIu')->updateOrInsert([
                    'idFuncion' => $funcion->idFuncion,
                    'idIu' => $iu->idIu
                ]);
            }
        }
        $funcionAreasCategorias = DB::table('funcion')->where('nombre', 'AreasCategorias')->first();

        // Obtener el ID de la IU 'AreaCategoria'
        $iuAreaCategoria = DB::table('iu')->where('nombreIu', 'AreaCategoria')->first();

        // Insertar la relación en la tabla funcionIu
        if ($funcionAreasCategorias && $iuAreaCategoria) {
            DB::table('funcionIu')->updateOrInsert([
                'idFuncion' => $funcionAreasCategorias->idFuncion,
                'idIu' => $iuAreaCategoria->idIu
            ]);
        }
        // Insertar grados  
        $gradosPrimaria = [
            '1ro de Primaria',
            '2do de Primaria',
            '3ro de Primaria',
            '4to de Primaria',
            '5to de Primaria',
            '6to de Primaria',
        ];

        $gradosSecundaria = [
            '1ro de Secundaria',
            '2do de Secundaria',
            '3ro de Secundaria',
            '4to de Secundaria',
            '5to de Secundaria',
            '6to de Secundaria',
        ];

        foreach (array_merge($gradosPrimaria, $gradosSecundaria) as $grado) {
            DB::table('grado')->updateOrInsert(['grado' => $grado]);
        }
        // Insertar categorías y asociar grados
        $categorias = [
            '3p' => ['3ro de Primaria'],
            '4p' => ['4to de Primaria'],
            '5P' => ['5to de Primaria'],
            '6P' => ['6to de Primaria'],
            '1S' => ['1ro de Secundaria'],
            '2S' => ['2do de Secundaria'],
            '3S' => ['3ro de Secundaria'],
            '4S' => ['4to de Secundaria'],
            '5S' => ['5to de Secundaria'],
            '6S' => ['6to de Secundaria'],
            'Guacamayo' => ['5to de Primaria', '6to de Primaria'],
            'Guanaco' => ['1ro de Secundaria', '2do de Secundaria', '3ro de Secundaria'],
            'Londra' => ['1ro de Secundaria', '2do de Secundaria', '3ro de Secundaria'],
            'Jucumari' => ['4to de Secundaria', '5to de Secundaria', '6to de Secundaria'],
            'Bufeo' => ['1ro de Secundaria', '2do de Secundaria', '3ro de Secundaria'],
            'Puma' => ['4to de Secundaria', '5to de Secundaria', '6to de Secundaria'],
            'Builders P' => ['5to de Secundaria', '6to de Secundaria'],
            'Builders S' => ['1ro de Secundaria', '2do de Secundaria', '3ro de Secundaria', '4to de Secundaria', '5to de Secundaria', '6to de Secundaria'],
            'Lego P' => ['5to de Secundaria', '6to de Secundaria'],
            'Lego S' => ['1ro de Secundaria', '2do de Secundaria', '3ro de Secundaria', '4to de Secundaria', '5to de Secundaria', '6to de Secundaria']
        ];

        foreach ($categorias as $categoria => $grados) {
            DB::table('categoria')->updateOrInsert(['nombre' => $categoria], ['nombre' => $categoria]);
            $categoriaId = DB::table('categoria')->where('nombre', $categoria)->value('idCategoria');

            foreach ($grados as $grado) {
                $gradoId = DB::table('grado')->where('grado', $grado)->value('idGrado');
                if ($gradoId) {
                    DB::table('gradoCategoria')->updateOrInsert([
                        'idGrado' => $gradoId,
                        'idCategoria' => $categoriaId
                    ]);
                }
            }
        }
        // Insertar áreas
        $areas = ['Física', 'Química', 'Matemáticas', 'Informática', 'Robótica', 'Biología', 'Astronomía'];
        foreach ($areas as $area) {
            DB::table('area')->updateOrInsert(['nombre' => $area]);
        }
        */
    }
}
