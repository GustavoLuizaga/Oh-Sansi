<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Funcion extends Migration
{

    public function up()
    {
        Schema::create('funcion', function (Blueprint $table) {
            $table->id('idFuncion');
            $table->string('nombre');
        });
    }

   
    public function down()
    {
        Schema::dropIfExists('funcion');
    }
}
