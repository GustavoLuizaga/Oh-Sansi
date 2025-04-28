<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {/*
        $this->call(RolSeeder::class);
        $this->call(PermisoIUSeeder::class);
        $this->call(AreaCategoriaGradoSeeder::class);
        $this->call(DelegacionSeeder::class);
        $this->call(ConvocatoriaSeeder::class);*/

        $this->call([
            RolSeeder::class,
            PermisoIUSeeder::class,
            AreaCategoriaGradoSeeder::class,
            DelegacionSeeder::class,
            ConvocatoriaSeeder::class,
        ]);
    }
}
