<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionPago extends Model
{
     protected $table = 'facturacion_pagos';
     protected $primaryKey = 'facturacion_pago_id';

     protected $fillable = [
          'tipo_pago_id',
          'facturacion_id',
          'monto',
          'numero_referencia',
          'create_at',
     ];

     public function facturaciones()
     {
          return $this->belongsTo(Facturacion::class, 'facturacion_id');
     }

     public function tipos_pagos()
     {
          return $this->belongsTo(TipoPago::class, 'tipo_pago_id');
     }
}
