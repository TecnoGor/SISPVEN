<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidenciaDetalle extends Model
{
    protected $table = 'incidencias_detalles';
    protected $primaryKey = "incidencia_detalle_id";

    protected $fillable = [
        'envio_incidencia_id',
        'incidencia_id'
    ];

    public function envios_incidencia(){
        return $this->belongsTo(EnvioIncidencia::class, 'envio_incidencia_id');
    }

    public function incidencia(){
        return $this->belongsTo(Incidencia::class, 'incidencia_id');
    }
}
