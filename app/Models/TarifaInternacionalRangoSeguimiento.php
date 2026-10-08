<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaInternacionalRangoSeguimiento extends Model
{
    use HasFactory;
    protected $table = 'tarifa_internacional_rangos_seguimiento';

    protected $fillable = [
        'tarifa_internacional_rango_seguimiento_id',
        'usuario_seguimiento_id',
        'tarifa_internacional_rango_id',
    ];
}
