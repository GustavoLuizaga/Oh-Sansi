<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorAreaDelegacion extends Model
{
    use HasFactory;

    protected $table = 'tutorAreaDelegacion';
    public $incrementing = false; // ya que la clave primaria es compuesta
    protected $primaryKey = ['id', 'idArea', 'idDelegacion'];
    public $timestamps = true;

    protected $fillable = [
        'id',
        'idArea',
        'idDelegacion',
        'tokenTutor',  // Make sure this matches your database column name
    ];

    // Relaciones (opcional)
    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'id', 'id');
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
