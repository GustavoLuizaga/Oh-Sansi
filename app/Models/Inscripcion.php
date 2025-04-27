<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripcion'; // nombre de la tabla
    protected $primaryKey = 'idInscripcion'; // clave primaria

    public $timestamps = false; // no hay columnas created_at ni updated_at

    protected $fillable = [
        'fechaInscripcion',
        'numeroContacto',
        'idGrado',
        'idConvocatoria',
        'idArea',
        'idDelegacion',
        'idCategoria',
        'nombreApellidosTutor',
        'correoTutor',
    ];

    // Relaciones opcionales
    public function grado()
    {
        return $this->belongsTo(Grado::class, 'idGrado', 'idGrado');
    }

    public function convocatoria()
    {
        return $this->belongsTo(Convocatoria::class, 'idConvocatoria', 'idConvocatoria');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'idArea', 'idArea');
    }

    public function delegacion()
    {
        return $this->belongsTo(Delegacion::class, 'idDelegacion', 'idDelegacion');
        
    }

    public function tutores()
    {
        return $this->belongsToMany(Tutor::class, 'tutorEstudianteInscripcion', 'idInscripcion', 'idTutor')
            ->withPivot('idEstudiante')
            ->withTimestamps();
    }

    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'tutorEstudianteInscripcion', 'idInscripcion', 'idEstudiante')
            ->withPivot('idTutor')
            ->withTimestamps();
    }
}
