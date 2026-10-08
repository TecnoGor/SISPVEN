<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Oficina extends Model
{
    use Notifiable;

    protected $table = 'oficinas';
    protected $primaryKey = 'oficina_id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'codigo',
        'oficina_relacionada_id',
        'nombre',
        'jefe_oficina',
        'tipo_oficina_id',
        'tamaño',
        'correo',
        'direccion',
        'telefono',
        'estado_id',
        'municipio_id',
        'parroquia_id',
        'zona_economica_especial',
        'latitud',
        'longitud',
        'estatus_id',
        'operaciones',
        'codigo_ubicacion',
        'externa',
        'centralizadora',
    ];


    public function oficinaRelacionada()
    {
        return $this->belongsTo(Oficina::class, 'oficina_relacionada_id');
    }

    public function tipo_oficina()
    {
        return $this->belongsTo(TipoOficina::class, 'tipo_oficina_id'); // Relación de muchos a uno
    }

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'oficina_servicio', 'oficina_id', 'servicio_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id', 'estado_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id', 'municipio_id');
    }

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class, 'parroquia_id', 'parroquia_id');
    }

    public function servicios_operativos()
    {
        return $this->belongsToMany(ServicioOperativo::class, 'oficina_servicio_operativo', 'oficina_id', 'servicio_operativo_id');
    }

    public function codigos_postales()
    {
        return $this->belongsToMany(CodigoPostal::class, 'oficina_codigo_postal', 'oficina_id', 'codigo_postal_id');
    }

    public function tipos_pagos()
    {
        return $this->belongsToMany(TipoPago::class, 'oficina_tipo_pago', 'oficina_id', 'tipo_pago_id');
    }

    public function facturaciones()
    {
        return $this->hasMany(Facturacion::class, 'facturacion_id');
    }

    public function estatus()
    {
        return $this->belongsTo(EstatusOficina::class, 'estatus_id');
    }

    public function semaforo_postal()
    {
        return $this->hasOne(OficinaSemaforoPostal::class, 'oficina_id', 'oficina_id');
    }

    public function envios()
    {
        return $this->hasMany(Envio::class, 'envio_id');
    }

    public function oficina_personal()
    {
        return $this->hasMany(OficinaPersonal::class, 'oficina_personal_id');
    }

    public function almacen()
    {
        return $this->hasMany(EnvioAlmacen::class, 'oficina_id', 'oficina_id');
    }

    public function apartado()
    {
        return $this->hasMany(CodigoApartadoPostal::class, 'oficina_id', 'oficina_id');
    }

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class, 'insumos_inventario', 'oficina_id', 'insumo_id')
            ->withPivot('cantidad')->withTimestamps();
    }


    public function gasto_operativo()
    {
        return $this->hasMany(GastoOperativo::class, 'oficina_id', 'oficina_id');
    }

    public function motivo_npc()
    {
        return $this->hasMany(MotivoNpcOficina::class, 'oficina_id', 'oficina_id');
    }

    public function pagos_servicios_publicos()
    {
        return $this->hasMany(PagoServicioPublico::class, 'oficina_id', 'oficina_id');
    }

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'oficina_id', 'oficina_id');
    }

    public function vehiculos_prestados()
    {
        return $this->belongsToMany(Vehiculo::class, 'oficinas_vehiculos', 'oficina_id', 'vehiculo_id')->withPivot('activo');
    }

    public function oficina_aliada()
    {
        return $this->hasOne(InformacionAliado::class, 'oficina_id');
    }

    public function gasto_arrendamiento()
    {
        return $this->hasMany(GastoArrendamiento::class, 'oficina_id', 'oficina_id');
    }
}
