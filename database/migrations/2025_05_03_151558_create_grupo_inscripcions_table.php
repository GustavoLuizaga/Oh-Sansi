<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrupoInscripcionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupo_inscripcions', function (Blueprint $table) {
            $table->id();
            $table->string('codigoInvitacion')->unique();
            $table->string('nombreGrupo')->nullable();
            $table->enum('modalidad', ['duo', 'equipo']);
            $table->enum('estado', ['activo', 'incompleto', 'cancelado'])->default('incompleto');
            $table->unsignedBigInteger('idDelegacion');
            $table->timestamps();

            $table->foreign('idDelegacion')->references('idDelegacion')->on('delegacion')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grupo_inscripcions');
    }
}
