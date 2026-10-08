<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiaSemana extends Model
{
    use HasFactory;

    // Definir la tabla asociada si el nombre no sigue la convención plural
    protected $table = 'dias_semana';

    // Definir la clave primaria
    protected $primaryKey = 'dia_semana_id';

    // Si la clave primaria no es un entero auto-incremental
    public $incrementing = true;

    // Los atributos que se pueden asignar masivamente
    protected $fillable = [
        'dia_semana',
    ];
}
