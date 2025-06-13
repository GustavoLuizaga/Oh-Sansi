<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delegacion extends Model
{
    protected $table = 'delegacion';
    protected $primaryKey = 'idDelegacion';

    protected $fillable = [
        'codigo_sie',
        'nombre',
        'dependencia',
        'departamento',
        'provincia',
        'municipio',
        'zona',
        'direccion',
        'telefono',
        'responsable_nombre',
        'responsable_email'
    ];
}
