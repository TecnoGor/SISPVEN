<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Servicio extends Model
{
    protected $table = 'servicios';
    protected $primaryKey = 'servicio_id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'activo',
        'cobra_entrega',
        'cobra_excedente',
        'cobra_almacenaje',
        'cobra_avisos_llegada',
        'nacional',
        'es_envio',
    ];

    public function oficinas()
    {
        return $this->belongsToMany(Oficina::class, 'oficina_servicio', 'servicio_id', 'oficina_id');
    }

    public function envios()
    {
        return $this->hasMany(Envio::class, 'envio_id');
    }

    public function tiposSacas()
    {
        return $this->belongsToMany(
            TipoSaca::class,
            'tipo_saca_servicio',
            'servicio_id',
            'tipo_saca_id'
        );
    }
}
