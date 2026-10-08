<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facturacion extends Model
{
    protected $table = 'facturaciones';
    protected $primaryKey = 'facturacion_id';

    protected $fillable = [
        'oficina_id',
        'nombre',
        'apellido',
        'tipo_documento',
        'documento',
        'direccion',
        'monto_total',
        'iva',
        'create_at',
    ];

    public function oficinas()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }

    public function facturacion_envios()
    {
        return $this->hasOne(FacturacionEnvio::class, 'facturacion_envio_id');
    }

    public function pagos()
    {
        return $this->hasMany(FacturacionPago::class, 'facturacion_id', 'facturacion_id');
    }
}

