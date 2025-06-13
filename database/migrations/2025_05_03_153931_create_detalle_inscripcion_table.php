<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleInscripcionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_inscripcion', function (Blueprint $table) {
            $table->id('idDetalleInscripcion');
            $table->enum('modalidadInscripcion', ['individual', 'duo', 'equipo'])->default('individual');
            $table->unsignedBigInteger('idInscripcion');  // Relación con la tabla inscripcion
            $table->unsignedBigInteger('idArea');  // Relación con la tabla area
            $table->unsignedBigInteger('idCategoria');  // Relación con la tabla categoria
            $table->unsignedBigInteger('idGrupoInscripcion')->nullable();  // Relación con la tabla grupo_inscripcions
            $table->timestamps();

            // Definimos las claves foráneas para las relaciones
            $table->foreign('idInscripcion')->references('idInscripcion')->on('inscripcion')->onDelete('cascade');
            $table->foreign('idArea')->references('idArea')->on('area')->onDelete('cascade');
            $table->foreign('idCategoria')->references('idCategoria')->on('categoria')->onDelete('cascade');
            $table->foreign('idGrupoInscripcion')->references('id')->on('grupo_inscripcions')->onDelete('set null');

            // Índices compuestos para asegurar la unicidad de las combinaciones
            $table->unique(['idInscripcion', 'idArea', 'idCategoria'], 'detalle_inscripcion_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_inscripcion');
    }
}
