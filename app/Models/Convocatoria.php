<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convocatoria extends Model
{
    use HasFactory;
        // Nombre de la tabla (por convención Laravel usaría 'convocatorias', pero la tuya es 'convocatoria')
        protected $table = 'convocatoria';

        // Nombre de la clave primaria (por defecto Laravel usa 'id', pero en tu caso es 'idConvocatoria')
        protected $primaryKey = 'idConvocatoria';
    
        // Los campos que pueden ser asignados masivamente
        protected $fillable = [
            'fechaInicio',
            'fechaFin',
            'contacto',
            'metodoPago',
            'estado',
        ];
    
        // Si no quieres usar `created_at` y `updated_at`, puedes desactivarlos
        public $timestamps = false;
}
