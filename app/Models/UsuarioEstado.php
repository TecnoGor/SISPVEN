<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioEstado extends Model
{
    use HasFactory;

    protected $table = 'usuario_estados'; // Nombre de la tabla

    protected $primaryKey = 'usuario_estado_id'; // Clave primaria

    // Si no se permite la asignación masiva de todos los campos, especifica los que se pueden asignar
    protected $fillable = [
        'id_user',
        'id_estado',
    ];

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    // Relación con el modelo Estado
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'estado_id'); // Asegúrate que 'estado_id' sea el campo correcto en la tabla 'estados'
    }
}