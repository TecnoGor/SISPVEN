<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
    use HasFactory;

    // Definir la tabla asociada si el nombre no sigue la convención plural
    protected $table = 'viajes';

    // Definir la clave primaria
    protected $primaryKey = 'viaje_id';

    // Si la clave primaria no es un entero auto-incremental
    public $incrementing = true;

    // Los atributos que se pueden asignar masivamente
    protected $fillable = [
        'ruta_id',
        'codigo',
        'dia_semana_id',
        'fecha_salida',
        'vehiculo_id',
        'proveedor_id',
        'propio',
        'activo',
    ];

    // Deshabilitar las marcas de tiempo si no se usan
    // public $timestamps = false;

    // Relaciones

    public function ruta()
    {
        return $this->belongsTo(RutasModelo::class, 'ruta_id', 'ruta_id');
    }

    public function diaSemana()
    {
        return $this->belongsTo(DiaSemana::class, 'dia_semana_id', 'dia_semana_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id', 'vehiculo_id');
    }
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'proveedor_id');
    }
    
}
