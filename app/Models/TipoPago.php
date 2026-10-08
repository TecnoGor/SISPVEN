<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    protected $table = 'tipos_pagos';
    protected $primaryKey = 'tipo_pago_id';
    public $timestamps = true;

    protected $fillable = ['nombre', 'activo', 'create_at'];

    public function oficinas()
    {
        $this->belongsToMany(Oficina::class, 'oficina_tipo_pago', 'tipo_pago_id', 'oficina_id');
    }


}
