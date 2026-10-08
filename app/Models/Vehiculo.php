<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mantenimiento; // Importación agregada

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $primaryKey = 'vehiculo_id';

    protected $fillable = [
        'proveedor_id',
        'oficina_id',
        'chofer_id',
        'user_id',
        'tipo_vehiculo_id',
        'placa',
        'color',
        'marca',
        'modelo',
        'año',
        'num_poliza',
        'fecha_vencimiento',
        'capacidad_carga',
        'Activo',
        'imagen',
        'kilometraje_actual', // <-- agregado
    ];

    // Relaciones
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'proveedor_id');
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function oficinas()
    {
        return $this->belongsToMany(Oficina::class,'oficinas_vehiculos','vehiculo_id','oficina_id')->using(OficinaVehiculo::class)->withPivot('activo');;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function chofer()
    {
        return $this->belongsTo(Chofer::class, 'chofer_id', 'chofer_id');
    }

    public function tipoVehiculo()
    {
        return $this->belongsTo(TipoVehiculo::class, 'tipo_vehiculo_id', 'tipo_vehiculo_id');
    }

    // 💡 Relación añadida para que funcione el with('mantenimientos')
    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'vehiculo_id', 'vehiculo_id');
    }

    // Relación con cargas de combustible
    public function cargasCombustible()
    {
        return $this->hasMany(CargaCombustible::class, 'vehiculo_id', 'vehiculo_id');
    }
}
