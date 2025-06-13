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
        'CodigoComprobante',
        'Comprobante_valido',
        'RutaComprobante',
    ];
    public $timestamps = true;


    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'idInscripcion', 'idInscripcion');
    }

    public function boletaPago()
    {
        return $this->belongsTo(BoletaPago::class, 'idBoleta', 'idBoleta');
        
    }
}