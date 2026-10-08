<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaNacionalRangoSeguimiento extends Model
{
    use HasFactory;

    protected $table = 'tarifa_nacionales_rangos_seguimientos';

    protected $fillable = [
        'tarifa_nacional_rango_seguimiento_id',
        'usuario_seguimiento_id',
        'tarifa_nacional_rango_id',
    ];
}
