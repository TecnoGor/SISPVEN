<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RutasModelo extends Model
{
    use HasFactory;

    protected $table = 'rutas'; // Nombre de la tabla en la base de datos

    protected $primaryKey = 'ruta_id'; // Clave primaria de la tabla

    public $timestamps = true; // Habilitar timestamps (created_at y updated_at)

    // Propiedades que se pueden llenar masivamente
    protected $fillable = [
        'distancia',
        'ruta',
        'oficina_id_origen',
        'oficina_id_destino',
        'siglas',
        'tiempo',
        'activo',
    ];

    // Cast para el campo 'activo' para tratarlo como booleano
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function puntosEntrega()
    {
        return $this->hasMany(RutaPuntoEntrega::class, 'ruta_id', 'ruta_id');
    }
    // Relación con el estado de origen
    public function oficinaOrigen()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id_origen', 'oficina_id');
    }

    public function oficinaDestino()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id_destino', 'oficina_id');
    }
    public function plataformaRutas()
    {
        return $this->hasMany(PlataformaRuta::class, 'ruta_id', 'ruta_id');
    }
    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'proveedores_rutas', 'ruta_id', 'proveedor_id');
    }
}