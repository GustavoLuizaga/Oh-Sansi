<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleInscripcion extends Model
{
    use HasFactory;
    protected $table = 'detalle_inscripcion';
    protected $primaryKey = 'idDetalle';

    protected $fillable = [
        'idInscripcion',
        'idArea',
        'idCategoria',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'idInscripcion', 'idInscripcion');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'idArea', 'idArea');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idCategoria', 'idCategoria');
    }
}
