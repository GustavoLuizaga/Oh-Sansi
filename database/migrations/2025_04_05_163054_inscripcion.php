<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Inscripcion extends Migration
{
    public function up()
    {
        Schema::create('inscripcion', function (Blueprint $table) {
            $table->id('idInscripcion');
            $table->date('fechaInscripcion');
            $table->integer('numeroContacto');
            $table->unsignedBigInteger('idGrado');
            $table->unsignedBigInteger('idConvocatoria');
            $table->unsignedBigInteger('idArea');
            $table->unsignedBigInteger('idDelegacion');
            $table->unsignedBigInteger('idCategoria');
            $table->string('nombreApellidosTutor', 100)->nullable();
            $table->string('correoTutor', 100)->nullable();
            $table->foreign('idGrado')->references('idGrado')->on('grado');
            $table->foreign('idConvocatoria')->references('idConvocatoria')->on('convocatoria');
            $table->foreign('idArea')->references('idArea')->on('area');
            $table->foreign('idDelegacion')->references('idDelegacion')->on('delegacion');
            $table->foreign('idCategoria')->references('idCategoria')->on('categoria');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inscripcion');
    }
}

