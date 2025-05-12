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
            $table->unsignedBigInteger('idGrado');
            $table->unsignedBigInteger('idConvocatoria');
            $table->unsignedBigInteger('idDelegacion');
            $table->string('nombreApellidosTutor', 100)->nullable();
            $table->string('correoTutor', 100)->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('idGrado')->references('idGrado')->on('grado');
            $table->foreign('idConvocatoria')->references('idConvocatoria')->on('convocatoria');
            $table->foreign('idDelegacion')->references('idDelegacion')->on('delegacion');
            
            // Con esto me aseguro que solo halla una inscripcion por convocatoria
            //$table->unique(['idConvocatoria'], 'unique_convocatoria');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inscripcion');
    }
}

