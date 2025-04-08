<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorAreaDelegacion extends Model
{
    use HasFactory;

    protected $table = 'tutorAreaDelegacion';
    protected $fillable = ['id', 'idArea', 'idDelegacion', 'tokenTutor'];

    // Deshabilitar el comportamiento de clave primaria compuesta para consultas simples
    protected $primaryKey = 'id';
    public $incrementing = false;

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'idArea', 'idArea');
    }

    public function delegacion()
    {
        return $this->belongsTo(Delegacion::class, 'idDelegacion', 'idDelegacion');
    }
}
