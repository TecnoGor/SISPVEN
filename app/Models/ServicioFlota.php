<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioFlota extends Model
{
    use HasFactory;

    // Nombre de la tabla asociada
    protected $table = 'servicios_flota';

    // Clave primaria de la tabla
    protected $primaryKey = 'servicios_flota_id';

    // Los atributos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
        'activo',
    ];

    // Atributos que deberían ser convertidos a tipos nativos
    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones, si las tienes en el futuro, las puedes agregar aquí
}
