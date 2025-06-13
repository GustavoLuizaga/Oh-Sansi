<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    protected $table = 'categoria';
    protected $primaryKey = 'idCategoria';
    protected $fillable = [
        'nombre',
    ];
    // Si no quieres usar `created_at` y `updated_at`, puedes desactivarlos
    public $timestamps = false;
    
    public function grados(){
        return $this->belongsToMany(Grado::class, 'gradocategoria', 'idCategoria', 'idGrado');
    }
    
    public function convocatoriaAreaCategorias()
    {
        return $this->hasMany(ConvocatoriaAreaCategoria::class, 'idCategoria', 'idCategoria');
    }

}
