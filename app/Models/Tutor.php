<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    use HasFactory;
    protected $table = 'tutor';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'profesion',
        'telefono',
        'linkRecurso',
        'tokenTutor',
        'es_director',
        'estado', 
    ];
    protected $casts = [
        'es_director' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function tutorAreaDelegacion()
    {
        return $this->hasOne(TutorAreaDelegacion::class, 'id');
    }

    public function areas()
    {
        return $this->belongsToMany(Area::class, 'tutorAreaDelegacion', 'id', 'idArea')
                    ->withPivot('idDelegacion', 'tokenTutor')
                    ->withTimestamps();
    }
    
    public function delegaciones()
    {
        return $this->belongsToMany(Delegacion::class,  'tutorAreaDelegacion','id','idDelegacion')->withTimestamps();
    }

    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'tutorEstudianteInscripcion', 'idTutor', 'idEstudiante')
            ->withPivot('idInscripcion')
            ->withTimestamps();
    }

}
