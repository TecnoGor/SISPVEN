<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaExpresoBolivariano extends Model
{
    use HasFactory;

    protected $table = 'tarifa_expreso_bolivariano';
    protected $primaryKey='tarifa_expreso_bolivariano_id';

    protected $fillable = [
        'desde',
        'hasta',
        'monto',
        'activo',
        'medida_id',
        'tipo_expreso',
        'servicios_id',
    ];
}
