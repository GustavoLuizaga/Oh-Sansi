<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Delegacion extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delegacion', function (Blueprint $table) {
            $table->id('idDelegacion');
            $table->string('codigo_sie', 20)->unique();
            $table->string('nombre', 100)->unique();
            $table->enum('dependencia', ['Fiscal', 'Convenio', 'Comunitaria', 'Privada']);
            $table->enum('departamento', [
                'Chuquisaca', 'La Paz', 'Cochabamba', 'Oruro', 'Potosí', 
                'Tarija', 'Santa Cruz', 'Beni', 'Pando'
            ]);
            $table->string('provincia', 40);
            $table->string('municipio', 40);
            $table->string('zona', 40);
            $table->string('direccion', 40);
            $table->integer('telefono');
            $table->string('responsable_nombre', 40);
            $table->string('responsable_email')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delegacion');
        
    }
}
