<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MantenimientoDetalle extends Model
{
    use HasFactory;

    protected $table = 'mantenimiento_detalle'; // Nombre de la tabla

    protected $primaryKey = 'mantenimiento_detalle_id'; // Clave primaria

    protected $fillable = [
        'mantenimiento_id',
        'servicios_flota_id',
        'fecha',
    ];

    /**
     * Relación con el modelo Mantenimiento
     */
    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class, 'mantenimiento_id', 'mantenimiento_id');
    }

    /**
     * Relación con el modelo ServiciosFlota
     */
    public function servicioFlota()
    {
        return $this->belongsTo(ServicioFlota::class, 'servicios_flota_id', 'servicios_flota_id');
    }
}
