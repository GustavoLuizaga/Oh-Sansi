<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
      /**
     * Relación muchos a muchos con tutores a través de tutorAreaDelegacion
     */
    public function tutores()
    {
        return $this->belongsToMany(Tutor::class, 'tutorareadelegacion', 'idConvocatoria', 'id')
                    ->withPivot('idArea', 'idDelegacion', 'tokenTutor')
                    ->withTimestamps();
    }
    
    /**
     * Obtener las áreas relacionadas con esta convocatoria a través de los tutores
     */
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'convocatoriaareacategoria', 'idConvocatoria', 'idArea')
            ->withTimestamps();
    }

    public function convocatoriaAreaCategorias()
    {
        return $this->hasMany(ConvocatoriaAreaCategoria::class, 'idConvocatoria', 'idConvocatoria')
            ->with(['area', 'categoria']); // Eager load las relaciones para mejor rendimiento
    }
}
