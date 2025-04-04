<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RolFuncion extends Migration
{
    public function up(){
        Schema::create('rolFuncion', function (Blueprint $table) {

            $table->unsignedBigInteger('idFuncion');
            $table->unsignedBigInteger('idRol');
            $table->timestamps();

            $table->foreign('idFuncion')->references('idFuncion')->on('funcion')->onDelete('cascade');
            $table->foreign('idRol')->references('idRol')->on('rol')->onDelete('cascade');
            
            $table->primary(['idFuncion', 'idRol']);
        });
    }

 
    public function down(){
        Schema::drop('rolFuncion');
    }
}
