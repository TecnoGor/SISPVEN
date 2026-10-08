<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecolectaPago extends Model
{
    use HasFactory;

    protected $table = 'recolecta_pagos';
    protected $primaryKey = 'recolecta_pago_id';

    public const ESTATUS_REPORTADO = 'reportado';
    public const ESTATUS_CONFIRMADO = 'confirmado';
    public const ESTATUS_RECHAZADO = 'rechazado';

    protected $fillable = [
        'recolecta_id',
        'tipo_pago_id',
        'monto',
        'numero_referencia',
        'banco_codigo',
        'telefono_pagador',
        'fecha_pago',
        'estatus',
        'confirmado_por',
        'confirmado_en',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'confirmado_en' => 'datetime',
    ];

    public function recolecta()
    {
        return $this->belongsTo(Recolecta::class, 'recolecta_id', 'recolecta_id');
    }

    public function tipoPago()
    {
        return $this->belongsTo(TipoPago::class, 'tipo_pago_id', 'tipo_pago_id');
    }
}
