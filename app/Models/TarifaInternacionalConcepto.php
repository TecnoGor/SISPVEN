<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaInternacionalConcepto extends Model
{
    use HasFactory;
    protected $table = 'tarifas_internacional_conceptos';
    protected $primaryKey='tarifa_conceptos_internacional_id';
    protected $fillable = [
        'nombre',
        'monto',
        'activo',
        'servicios_id',
    ];
}
