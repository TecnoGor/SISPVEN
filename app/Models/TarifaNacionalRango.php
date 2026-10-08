<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaNacionalRango extends Model
{
    use HasFactory;

    protected $table = 'tarifa_nacionales_rangos';
    protected $primaryKey='tarifa_nacional_rango_id';

    protected $fillable = [
        'desde',
        'hasta',
        'monto',
        'activo',
        'medida_id',
        'servicios_id',
    ];
}
