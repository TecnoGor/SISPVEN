<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaNacionalConcepto extends Model
{
    use HasFactory;

    protected $table = 'tarifas_nacionales_conceptos';
    protected $primaryKey='tarifa_conceptos_id';

    protected $fillable = [
        'nombre',
        'monto',
        'exclusion',
        'activo',
        'servicios_id',
    ];
}
