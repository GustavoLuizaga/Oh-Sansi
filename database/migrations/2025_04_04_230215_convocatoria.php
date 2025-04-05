<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Convocatoria extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('convocatoria', function (Blueprint $table) {
            $table->id('idConvocatoria');
            $table->date('fechaInicio');
            $table->date('fechaFin');
            $table->string('contacto');
            $table->string('metodoPago');
            $table->boolean('estado');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('convocatoria');
        
    }
}
