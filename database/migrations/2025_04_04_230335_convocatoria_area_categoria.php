<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConvocatoriaAreaCategoria extends Migration
{
    public function up()
    {
        Schema::create('convocatoriaAreaCategoria', function (Blueprint $table) {

            $table->unsignedBigInteger('idConvocatoria');
            $table->unsignedBigInteger('idArea');
            $table->unsignedBigInteger('idCategoria');
            $table->decimal('precio', 8, 2); // Changed from integer to decimal
            $table->timestamps();

            $table->foreign('idConvocatoria')->references('idConvocatoria')->on('convocatoria')->onDelete('cascade');
            $table->foreign('idArea')->references('idArea')->on('area')->onDelete('cascade');
            $table->foreign('idCategoria')->references('idCategoria')->on('categoria')->onDelete('cascade');
            
            $table->primary(['idConvocatoria', 'idArea', 'idCategoria'], 'convocatoria_area_categoria_pk');
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('convocatoriaAreaCategoria');
    }
}
