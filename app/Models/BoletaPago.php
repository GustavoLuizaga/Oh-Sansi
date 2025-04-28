<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoletaPago extends Model
{
    use HasFactory;
    protected $table = 'boletapago';
    protected $primaryKey = 'idBoleta';
    public $timestamps = false;
    protected $fillable = [
        'CodigoBoleta',
        'MontoBoleta',
        'fechainicio',
        'fechafin',
        
    ];
}


