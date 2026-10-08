<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $table = 'mantenimiento'; // Nombre de la tabla

    protected $primaryKey = 'mantenimiento_id'; // Clave primaria

    protected $fillable = [
        'vehiculo_id',
        'descripcion',
        'fecha',
        'activo',
        'kilometraje',
        'costo',
    ];

    /**
     * Relación con el modelo Vehiculo
     */
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id', 'vehiculo_id');
    }

    /**
     * Accesor para obtener el estado como texto.
     */
    public function getEstadoAttribute()
    {
        return $this->activo ? 'Activo' : 'Inactivo';
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(MantenimientoDetalle::class, 'mantenimiento_id', 'mantenimiento_id');
    }
}
