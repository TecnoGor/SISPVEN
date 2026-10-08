<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionTarifaEnvio extends Model
{
    protected $table = 'facturacion_tarifas_envios';
    protected $primaryKey = 'facturacion_tarifas_envios_id';

    protected $fillable =[
        'tarifa_id',
        'envio_id',
        'envio_exporta_facil_id',
        'tipo_envio',
    ];

    public function facturacion()
    {
        return $this->belongsTo(Facturacion::class, 'facturacion_id');
    }

    public function tarifa()
    {
        return $this->belongsTo(TarifaNacionalConcepto::class, 'tarifa_id');
    }

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    }

    public function exporta_facil()
    {
        return $this->belongsTo(EnvioExportaFacil::class, 'envio_exporta_facil_id');
    }
}
