<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(): void
    {
        Schema::create('verificacioninscripcion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idInscripcion');
            $table->unsignedBigInteger('idBoleta');
            $table->integer('CodigoComprobante')->nullable();
            $table->boolean('Comprobante_valido')->default(false)->nullable();
            $table->string('RutaComprobante')->nullable();
            $table->timestamps();

            // Claves foráneas
            $table->foreign('idInscripcion')->references('idInscripcion')->on('inscripcion')->onDelete('cascade');
            $table->foreign('idBoleta')->references('idBoleta')->on('boletapago')->onDelete('cascade');
            
            // Índice para optimizar consultas
            $table->index(['idInscripcion', 'idBoleta']);
            // Índices para mejorar el rendimiento
            // $table->index('idInscripcion');
            // $table->index('idBoleta');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('verificacioninscripcion');
    }
};