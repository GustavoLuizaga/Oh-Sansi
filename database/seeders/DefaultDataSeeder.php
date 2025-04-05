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
    {
        // Crear roles si no existen
        $roles = ['Administrador', 'Tutor', 'Estudiante'];

        foreach ($roles as $rol) {
            DB::table('rol')->updateOrInsert(
                ['nombre' => $rol]
            );
        }

        // Obtener ID del rol administrador
        $rolAdmin = DB::table('rol')->where('nombre', 'Administrador')->first();

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
        // Insertar funciones
        $funciones = ['Dashboard', 'Notificaciones', 'Delegaciones', 'Convocatoria', 'Registro', 'AreasCategorias', 'Perfil', 'Seguridad'];
        foreach ($funciones as $funcion) {
            DB::table('funcion')->updateOrInsert(['nombre' => $funcion]);
        }

        // Insertar interfaces de usuario (IU)
        $ius = ['Dashboard', 'Notificaciones', 'Delegaciones', 'Convocatoria', 'Registro', 'AreaCategoria', 'Perfil', 'Seguridad'];
        foreach ($ius as $iu) {
            DB::table('iu')->updateOrInsert(['nombreIu' => $iu]);
        }
        // Obtener roles
        $rolAdminId = DB::table('rol')->where('nombre', 'administrador')->value('idRol');
        $rolEstudianteId = DB::table('rol')->where('nombre', 'estudiante')->value('idRol');

        // Obtener funciones
        $funcionesAll = DB::table('funcion')->get();
        $funcionesEstudiante = DB::table('funcion')
            ->whereIn('nombre', ['Dashboard', 'Convocatoria'])
            ->get();
        // Relacionar Administrador con todas las funciones
        foreach ($funcionesAll as $funcion) {
            DB::table('rolFuncion')->updateOrInsert([
                'idFuncion' => $funcion->idFuncion,
                'idRol' => $rolAdminId
            ]);
        }
        // Relacionar Estudiante con Dashboard y Convocatoria
        foreach ($funcionesEstudiante as $funcion) {
            DB::table('rolFuncion')->updateOrInsert([
                'idFuncion' => $funcion->idFuncion,
                'idRol' => $rolEstudianteId
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
    }
}
