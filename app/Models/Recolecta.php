<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recolecta extends Model
{
    use HasFactory;

    protected $table = 'recolectas';
    protected $primaryKey = 'recolecta_id';

    protected $fillable = [
        'usuario_app_id',
        'oficina_id',
        'envio_id',
        'recolecta_estatus_id',
        'codigo',
        'nombre_rem',
        'apellido_rem',
        'tipo_documento_rem',
        'documento_rem',
        'telefono_rem',
        'correo_rem',
        'estado_id',
        'municipio_id',
        'parroquia_id',
        'ciudad_id',
        'codigo_postal',
        'direccion',
        'referencia',
        'latitud',
        'longitud',
        'precision_gps',
        'gps_manual',
        'nombre_dest',
        'apellido_dest',
        'tipo_documento_dest',
        'documento_dest',
        'telefono_dest',
        'correo_dest',
        'estado_dest_id',
        'municipio_dest_id',
        'parroquia_dest_id',
        'ciudad_dest_id',
        'codigo_postal_dest',
        'direccion_dest',
        'modo_peso',
        'peso',
        'alto',
        'ancho',
        'largo',
        'contenido',
        'monto_envio',
        'monto_recoleccion',
        'iva',
        'total',
        'tasa_bs',
        'excede_tarifa_max',
        'pago_confirmado_en',
        'pago_confirmado_por',
        'motivo_rechazo',
    ];

    protected $casts = [
        'gps_manual' => 'boolean',
        'excede_tarifa_max' => 'boolean',
        'pago_confirmado_en' => 'datetime',
    ];

    public function usuarioApp()
    {
        return $this->belongsTo(UsuarioAppMovil::class, 'usuario_app_id', 'usuario_id');
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id', 'envio_id');
    }

    public function estatus()
    {
        return $this->belongsTo(RecolectaEstatus::class, 'recolecta_estatus_id', 'recolecta_estatus_id');
    }

    public function pagos()
    {
        return $this->hasMany(RecolectaPago::class, 'recolecta_id', 'recolecta_id');
    }

    public function estadoOrigen()
    {
        return $this->belongsTo(Estado::class, 'estado_id', 'estado_id');
    }

    public function estadoDestino()
    {
        return $this->belongsTo(Estado::class, 'estado_dest_id', 'estado_id');
    }

    public function scopeDeUsuarioApp($query, int $usuarioAppId)
    {
        return $query->where('usuario_app_id', $usuarioAppId);
    }

    public function scopeDeOficina($query, int $oficinaId)
    {
        return $query->where('oficina_id', $oficinaId);
    }

    public function tieneEstatus(string $slug): bool
    {
        return $this->estatus?->slug === $slug;
    }
}
