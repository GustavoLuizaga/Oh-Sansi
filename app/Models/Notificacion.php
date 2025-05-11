<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificacions';

    protected $fillable = [
        'user_id',
        'mensaje',
        'tipo',
    ];

    /**
     * Relación: Una notificación pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}