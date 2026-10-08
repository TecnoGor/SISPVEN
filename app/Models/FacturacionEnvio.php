<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionEnvio extends Model
{
    protected $table = 'facturacion_envio';
    protected $primaryKey = 'facturacion_envio_id';

    protected $fillable = [
        'facturacion_id',
        'envio_id',
        'usuario_id',
        'oficina_id',
        'servicio_id',
        'envio_exporta_facil_id',
        'create_at',
    ];

    public function facturaciones()
    {
        return $this->belongsTo(Facturacion::class, 'facturacion_id');
    }

    public function envios()
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    }

    public function exporta_facil()
    {
        return $this->belongsTo(EnvioExportaFacil::class, 'envio_exporta_facil_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }
}
