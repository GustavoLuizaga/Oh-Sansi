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
            $table->enum('status', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->unsignedBigInteger('idGrado');  // Grado del estudiante
            $table->unsignedBigInteger('idConvocatoria');  // Convocatoria asociada
            $table->unsignedBigInteger('idDelegacion');  // Delegación asociada
            $table->string('nombreApellidosTutor', 100)->nullable();  // Nombre del tutor
            $table->string('correoTutor', 100)->nullable();  // Correo del tutor
            $table->foreign('idGrado')->references('idGrado')->on('grado');
            $table->foreign('idConvocatoria')->references('idConvocatoria')->on('convocatoria');
            $table->foreign('idDelegacion')->references('idDelegacion')->on('delegacion');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inscripcion');
    }
}

