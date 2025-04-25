<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DelegacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $delegaciones = [
            [
                'codigo_sie' => '10000001',
                'nombre' => 'Unidad Educativa San Martín',
                'dependencia' => 'Fiscal',
                'departamento' => 'La Paz',
                'provincia' => 'Murillo',
                'municipio' => 'La Paz',
                'zona' => 'Villa Fátima',
                'direccion' => 'Calle 10 Esq. 12',
                'telefono' => 2221234,
                'responsable_nombre' => 'Juan Pérez',
                'responsable_email' => 'juan.perez1@example.com',
            ],
            [
                'codigo_sie' => '10000002',
                'nombre' => 'Unidad Educativa Copacabana',
                'dependencia' => 'Convenio',
                'departamento' => 'La Paz',
                'provincia' => 'Omasuyos',
                'municipio' => 'Copacabana',
                'zona' => 'Centro',
                'direccion' => 'Av. 6 de Agosto',
                'telefono' => 2225678,
                'responsable_nombre' => 'María Luque',
                'responsable_email' => 'maria.luque@example.com',
            ],
            [
                'codigo_sie' => '10000003',
                'nombre' => 'Unidad Educativa Santa Cruz de la Sierra',
                'dependencia' => 'Privada',
                'departamento' => 'Santa Cruz',
                'provincia' => 'Andrés Ibáñez',
                'municipio' => 'Santa Cruz de la Sierra',
                'zona' => 'Equipetrol',
                'direccion' => 'Calle Beni #20',
                'telefono' => 3341122,
                'responsable_nombre' => 'Carlos López',
                'responsable_email' => 'carlos.lopez@example.com',
            ],
            [
                'codigo_sie' => '10000004',
                'nombre' => 'Unidad Educativa San Andrés',
                'dependencia' => 'Fiscal',
                'departamento' => 'Cochabamba',
                'provincia' => 'Cercado',
                'municipio' => 'Cochabamba',
                'zona' => 'Queru Queru',
                'direccion' => 'Av. Circunvalación #23',
                'telefono' => 4423111,
                'responsable_nombre' => 'Elena Ríos',
                'responsable_email' => 'elena.rios@example.com',
            ],
            [
                'codigo_sie' => '10000005',
                'nombre' => 'Unidad Educativa Tupac Katari',
                'dependencia' => 'Fiscal',
                'departamento' => 'Oruro',
                'provincia' => 'Cercado',
                'municipio' => 'Oruro',
                'zona' => 'Zona Norte',
                'direccion' => 'Calle Bolívar #46',
                'telefono' => 5255678,
                'responsable_nombre' => 'Mario Mamani',
                'responsable_email' => 'mario.mamani@example.com',
            ],
            [
                'codigo_sie' => '10000006',
                'nombre' => 'Unidad Educativa Simón Bolívar',
                'dependencia' => 'Privada',
                'departamento' => 'Tarija',
                'provincia' => 'Cercado',
                'municipio' => 'Tarija',
                'zona' => 'San Blas',
                'direccion' => 'Av. La Madrid #78',
                'telefono' => 4663344,
                'responsable_nombre' => 'Sofía Vargas',
                'responsable_email' => 'sofia.vargas@example.com',
            ],
            [
                'codigo_sie' => '10000007',
                'nombre' => 'Unidad Educativa Mariscal Sucre',
                'dependencia' => 'Fiscal',
                'departamento' => 'Chuquisaca',
                'provincia' => 'Oropeza',
                'municipio' => 'Sucre',
                'zona' => 'Zona Central',
                'direccion' => 'Calle Abaroa #10',
                'telefono' => 4647788,
                'responsable_nombre' => 'Luis Arce',
                'responsable_email' => 'luis.arce@example.com',
            ],
            [
                'codigo_sie' => '10000008',
                'nombre' => 'Unidad Educativa Franz Tamayo',
                'dependencia' => 'Convenio',
                'departamento' => 'Beni',
                'provincia' => 'Cercado',
                'municipio' => 'Trinidad',
                'zona' => 'Barrio Belén',
                'direccion' => 'Av. 18 de Noviembre #50',
                'telefono' => 3885566,
                'responsable_nombre' => 'Teresa Flores',
                'responsable_email' => 'teresa.flores@example.com',
            ],
            [
                'codigo_sie' => '10000009',
                'nombre' => 'Unidad Educativa Germán Busch',
                'dependencia' => 'Fiscal',
                'departamento' => 'Pando',
                'provincia' => 'Nicolás Suárez',
                'municipio' => 'Cobija',
                'zona' => 'Zona Comercial',
                'direccion' => 'Calle Comercio #77',
                'telefono' => 3822233,
                'responsable_nombre' => 'Julio Castro',
                'responsable_email' => 'julio.castro@example.com',
            ],
            [
                'codigo_sie' => '10000010',
                'nombre' => 'Unidad Educativa Eduardo Abaroa',
                'dependencia' => 'Fiscal',
                'departamento' => 'Potosí',
                'provincia' => 'Tomás Frías',
                'municipio' => 'Potosí',
                'zona' => 'San Benito',
                'direccion' => 'Av. Serrudo #33',
                'telefono' => 2621122,
                'responsable_nombre' => 'Norma Quispe',
                'responsable_email' => 'norma.quispe@example.com',
            ],
            [
                'codigo_sie' => '10000011',
                'nombre' => 'Unidad Educativa Juan XXIII',
                'dependencia' => 'Convenio',
                'departamento' => 'La Paz',
                'provincia' => 'Murillo',
                'municipio' => 'El Alto',
                'zona' => 'Ciudad Satélite',
                'direccion' => 'Calle 3 Oeste #88',
                'telefono' => 2845566,
                'responsable_nombre' => 'Rodrigo Nina',
                'responsable_email' => 'rodrigo.nina@example.com',
            ],
        ];

        foreach ($delegaciones as $delegacion) {
            DB::table('delegacion')->insert([
                'codigo_sie' => $delegacion['codigo_sie'],
                'nombre' => $delegacion['nombre'],
                'dependencia' => $delegacion['dependencia'],
                'departamento' => $delegacion['departamento'],
                'provincia' => $delegacion['provincia'],
                'municipio' => $delegacion['municipio'],
                'zona' => $delegacion['zona'],
                'direccion' => $delegacion['direccion'],
                'telefono' => $delegacion['telefono'],
                'responsable_nombre' => $delegacion['responsable_nombre'],
                'responsable_email' => $delegacion['responsable_email'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
