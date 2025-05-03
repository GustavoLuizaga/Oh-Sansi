<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorEstudianteInscripcion extends Model
{
    use HasFactory;
    protected $table = 'tutorEstudianteInscripcion'; // Nombre real de la tabla

    protected $fillable = [
        'idEstudiante',
        'idTutor',
        'idInscripcion',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'idInscripcion', 'idInscripcion');
    }

    public function obtenerInscripcionesPorTutor($idTutor)
    {
        return $this->where('idTutor', $idTutor)
            ->with('inscripcion')
            ->get();
    }

    public function puedeInscribirseEnMasAreas($idEstudiante, $idConvocatoria)
    {
        $cantidadInscripciones = $this->where('idEstudiante', $idEstudiante)
            ->whereHas('inscripcion', function($query) use ($idConvocatoria) {
                $query->where('idConvocatoria', $idConvocatoria);
            })
            ->count();

        return $cantidadInscripciones < 2;
    }

    /**
     * Obtiene la cantidad de áreas inscritas en una convocatoria
     * @param int $idEstudiante
     * @param int $idConvocatoria
     * @return int
     */
    public function cantidadAreasInscritas($idEstudiante, $idConvocatoria)
    {
        return $this->where('idEstudiante', $idEstudiante)
            ->whereHas('inscripcion', function($query) use ($idConvocatoria) {
                $query->where('idConvocatoria', $idConvocatoria);
            })
            ->count();
    }
}
