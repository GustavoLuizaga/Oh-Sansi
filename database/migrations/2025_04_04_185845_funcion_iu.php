<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FuncionIu extends Migration
{
    public function up()
    {
        Schema::create('funcionIu', function (Blueprint $table) {

            $table->unsignedBigInteger('idFuncion');
            $table->unsignedBigInteger('idIu');
            $table->timestamps();

            $table->foreign('idFuncion')->references('idFuncion')->on('funcion')->onDelete('cascade');
            $table->foreign('idIu')->references('idIu')->on('iu')->onDelete('cascade');
            
            $table->primary(['idFuncion', 'idIu']);
        });
    }


    public function down()
    {
        Schema::drop('funcionIu');
    }
}
