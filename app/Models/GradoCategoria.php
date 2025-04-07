<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class GradoCategoria extends Model
{
    use HasFactory;
    protected $table = 'gradocategoria'; 
    protected $fillable = [
        'idGrado',
        'idCategoria',
     ];
    public $timestamps = true;


    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idCategoria', 'idCategoria');
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'idGrado', 'idGrado');
    }

}
