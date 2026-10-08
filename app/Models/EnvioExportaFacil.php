<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioExportaFacil extends Model
{
    use HasFactory;

    protected $table = 'envios_exporta_facil';
    protected $primaryKey = 'envio_exporta_facil_id';
    protected $fillable = ['envio_id', 'clase_correo', 'estado_dest', 'parroquia_dest', 'ciudad_dest', 'codigo_postal_dest', ];

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class, 'parroquia_id');
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }

    public function facturacion_envio()
    {
        return $this->hasOne(FacturacionEnvio::class, 'envio_exporta_facil_id');
    }

    public function facturacion_detalle()
    {
        return $this->hasOne(FacturacionDetalle::class, 'servicio_id');
    }

    public function facturacion_tarifa_envio()
    {
        return $this->hasOne(FacturacionTarifaEnvio::class, 'envio_exporta_facil_id');
    }

}
