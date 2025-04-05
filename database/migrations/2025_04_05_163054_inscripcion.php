<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Inscripcion extends Migration
{
    public function up(){
        Schema::create('inscripcion', function (Blueprint $table) {
            $table->id('idInscripcion');
            $table->date('fechaInscripcion');
            $table->integer('numeroContacto');
            $table->unsignedBigInteger('idGrado');
            $table->unsignedBigInteger('idConvocatoria');
            $table->unsignedBigInteger('idArea');
            $table->unsignedBigInteger('idDelegacion');           
            $table->foreign('idGrado')->references('idGrado')->on('grado')->onDelete('cascade');
            $table->foreign('idConvocatoria')->references('idConvocatoria')->on('convocatoria')->onDelete('cascade');
            $table->foreign('idArea')->references('idArea')->on('area')->onDelete('cascade');
            $table->foreign('idDelegacion')->references('idDelegacion')->on('delegacion')->onDelete('cascade');
        });
        
    } 


    public function down()
    {
        Schema::dropIfExists('inscripcion');
        
    }
}
