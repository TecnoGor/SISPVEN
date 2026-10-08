<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionServicioPago extends Model
{
    use HasFactory;

    protected $table = 'facturacion_servicios_pagos';
    protected $primaryKey = 'facturacion_servicios_pagos_id';

    protected $fillable = [
        'facturacion_servicio_id',
        'tipo_pago_id',
        'monto',
        'referencia_bancaria',
    ];

    public function facturacionServicio()
    {
        return $this->belongsTo(FacturacionServicio::class, 'facturacion_servicio_id');
    }
}
