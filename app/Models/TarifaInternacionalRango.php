<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaInternacionalRango extends Model
{
    use HasFactory;
    protected $table = 'tarifa_internacional_rangos';
    protected $primaryKey='tarifa_internacional_rango_id';

    protected $fillable = [
        'desde',
        'hasta',
        'grupo',
        'monto',
        'activo',
        'medida_id',
        'servicios_id',
    ];
}
