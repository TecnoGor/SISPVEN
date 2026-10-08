<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaInternacionalConceptoSeguimiento extends Model
{
    use HasFactory;
    protected $table = 'tarifas_internacional_conceptos_seguimientos';
    protected $primaryKey = 'tarifa_internacional_concepto_seguimiento_id';
    

    protected $fillable = [
        'usuario_seguimiento_id',
        'tarifa_conceptos_internacional_id',
    ];
}
