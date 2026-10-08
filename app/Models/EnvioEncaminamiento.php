<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioEncaminamiento extends Model
{
    use HasFactory;

    protected $table = 'envios_encaminamiento';
    protected $primaryKey = 'envios_encaminamiento_id';
    protected $fillable = [
        'envio_id',
        'oficina_id',
        'oficina_externa_id',
        'usuario_id',
        'viaje_id',
        'estatus_id',
        'manifiesto_id',
        'devolucion'
    ];


    public function envios()
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    }

    public function oficinas()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function oficina_externa()
    {
        return $this->belongsTo(Oficina::class, 'oficina_externa_id', 'oficina_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function viajes()
    {
        return $this->belongsTo(Oficina::class, 'viaje_id');
    }

    public function envio_estatus()
    {
        return $this->belongsTo(EnvioEstatus::class, 'estatus_id');
    }

    public function exporta_facil()
    {
        return $this->belongsTo(EnvioExportaFacil::class, 'envio_exporta_facil_id');
    }
}
