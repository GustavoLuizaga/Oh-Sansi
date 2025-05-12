<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convocatoria extends Model
{
    use HasFactory;
    
    protected $table = 'convocatoria';
    protected $primaryKey = 'idConvocatoria';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'fechaInicio',
        'fechaFin',
        'contacto',
        'requisitos',
        'metodoPago',
        'estado',
    ];
    
    public $timestamps = true;
}
