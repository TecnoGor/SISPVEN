<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaExpresoBolivarianoSeguimiento extends Model
{
    use HasFactory;

    protected $table = 'tarifa_expreso_bolivariano_seguimiento';

    protected $primaryKey = 'tarifa_expreso_bolivariano_seguimiento_id';

    protected $fillable = [
        'usuario_seguimiento_id',
        'tarifa_expreso_bolivariano_id',
    ];
}
