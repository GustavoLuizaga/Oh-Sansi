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
        'status',
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

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idCategoria', 'idCategoria');
    }

    public function obtenerEstudiantePorInscripcion($idInscripcion)
    {
        return $this->estudiantes()
            ->where('inscripcion.idInscripcion', $idInscripcion)
            ->first();
    }

    public function puedeInscribirse($idEstudiante)
    {
        $tutorEstudianteInscripcion = new TutorEstudianteInscripcion();
        return $tutorEstudianteInscripcion->puedeInscribirseEnMasAreas($idEstudiante, $this->idConvocatoria);
    }

    /**
     * Obtiene las áreas en las que está inscrito un estudiante en esta convocatoria
     * @param int $idEstudiante
     * @return Collection
     */
    public function obtenerAreasInscritas($idEstudiante)
    {
        return $this->where('idConvocatoria', $this->idConvocatoria)
            ->whereHas('estudiantes', function($query) use ($idEstudiante) {
                $query->where('estudiante.id', $idEstudiante);
            })
            ->with('area')
            ->get()
            ->pluck('area');
    }
}
