<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripcion';
    protected $primaryKey = 'idInscripcion';
    public $timestamps = false;

    protected $fillable = [
        'fechaInscripcion',
        'numeroContacto',
        'status',
        'idGrado',
        'idConvocatoria',
        'idDelegacion',
        'nombreApellidosTutor',
        'correoTutor',
    ];

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'idGrado', 'idGrado');
    }

    public function convocatoria()
    {
        return $this->belongsTo(Convocatoria::class, 'idConvocatoria', 'idConvocatoria');
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

    public function detalles()
    {
        return $this->hasMany(DetalleInscripcion::class, 'idInscripcion', 'idInscripcion');
    }
}

