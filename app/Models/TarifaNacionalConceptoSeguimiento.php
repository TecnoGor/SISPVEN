<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaNacionalConceptoSeguimiento extends Model
{
    use HasFactory;

    protected $table = 'tarifas_nacionales_conceptos_seguimientos';

    protected $fillable = [
        'tarifa_nacional_concepto_seguimiento_id',
        'usuario_seguimiento_id',
        'tarifa_conceptos_id',
    ];
}
