<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificacionInscripcion extends Model
{
    use HasFactory;
    protected $table = 'verificacioninscripcion'; // nombre de la tabla
    protected $fillable = [
        'idInscripcion',
        'idBoleta',
        'valido',
        'imagen_comprobante',
    ];
    //public $timestamps = true;


    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'idInscripcion', 'idInscripcion');
    }

    public function boletaPago()
    {
        return $this->belongsTo(boletaPago::class, 'idBoleta', 'idBoleta');
        
    }
}

