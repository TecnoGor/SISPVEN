<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformacionAliado extends Model
{
    protected $table = 'informacion_aliados';
    protected $primaryKey = 'informacion_aliado_id';
    protected $fillable = [
        'oficina_id',
        'RIF',
        'nro_contrato',
        'fecha_contratacion',
        'tarifa_aplicada',
        'tipo_facturacion',
        'tiempo_entrega_zona',
        'tiempo_entrega_estado',
    ];

    public function oficina(){
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }

    use HasFactory;
}
