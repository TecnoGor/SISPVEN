<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioAlmacen extends Model
{
    protected $table = 'envios_almacen';
    protected $primaryKey = 'envio_almacen_id';
    protected $fillable = ['oficina_id', 'envio_id', 'codigo', 'saca_id', 'estatus', 'Entrada', 'Salida'];



    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id', 'envio_id');
    }

    public function envio_internacional()
    {
        return $this->belongsTo(EnvioInternacional::class, 'envio_id', 'envio_id');
    }

    public function saca()
    {
        return $this->belongsTo(Saca::class, 'saca_id');
    }

    public function aviso()
    {
        return $this->hasMany(AlmacenAviso::class, 'envio_almacen_id', 'envio_almacen_id');
    }

    public function facturacion_destinatario()
    {
        return $this->hasMany(AlmacenAviso::class, 'envio_almacen_id');
    }

    public function carteros()
    {
        return $this->belongsToMany(User::class, 'asignacion_envio_cartero', 'envio_almacen_id', 'user_id')
                    ->withPivot('estatus')
                    ->withTimestamps();
    }

    public function registro_entrega()
    {
        return $this->belongsTo(RegistroEntrega::class, 'envio_id', 'envio_id');
    }


    use HasFactory;
}
