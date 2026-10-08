<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoCorporativoDetalle extends Model
{
    use HasFactory;

    protected $table = 'contratos_corporativos_detalles';
    protected $primaryKey = 'contrato_corporativo_detalle_id';
    protected $fillable = [
        'cliente_corporativo_id',
        'contrato_corporativo_id',
        'contrato_almacenamiento_id',
        'cuota',
        'fecha_limite',
        'tasa_pago',
        'monto_bs_pagado',
        'cancelada',
        'fecha_cancelada',
        'created_at',
        'updated_at'
    ];

    public function cliente()
    {
        return $this->belongsTo(ClienteCorporativo::class, 'cliente_corporativo_id');
    }

    public function contrato()
    {
        return $this->belongsTo(ContratoCorporativo::class, 'contrato_corporativo_id');
    }

    public function almacenamiento()
    {
        return $this->belongsTo(ContratoAlmacenamiento::class, 'contrato_almacenamiento_id');
    }
}
