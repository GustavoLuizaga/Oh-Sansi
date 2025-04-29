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

}
