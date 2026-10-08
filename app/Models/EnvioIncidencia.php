<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioIncidencia extends Model
{
    protected $table = "envios_incidencias";
    protected $primaryKey = "envio_incidencia_id";

    protected $fillable = [
        'envio_id',
        'detalle',
        'imagen_incidencia',
        'usuario_id',
        'oficina_id'
    ];


    public function incidencias_detalles(){
        return $this->hasMany(IncidenciaDetalle::class, 'envio_incidencia_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id', 'envio_id');
    }

    public function envio_almacen()
    {
        return $this->hasOne(EnvioAlmacen::class, 'envio_id');
    }
}
