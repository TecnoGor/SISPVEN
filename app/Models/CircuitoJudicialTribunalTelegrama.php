<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircuitoJudicialTribunalTelegrama extends Model
{
    protected $table = 'circuito_judicial_tribunal_telegramas';
    protected $primaryKey = 'circuito_judicial_tribunal_telegramas_id';
    public $timestamps = true;

    protected $fillable = [
        'lugar_emision_telegramas_id',
        'nombre',
        'activo', 
    ];

    /* Relación: este registro pertenece a un Lugar de Emisión */
    public function lugar()
    {
        return $this->belongsTo(
            \App\Models\LugarEmisionTelegrama::class,
            'lugar_emision_telegramas_id',
            'lugar_emision_telegramas_id'
        );
    }

    /* Scope para filtrar por lugar de emisión */
    public function scopeDeLugar($query, $lugarId)
    {
        return $query->where('lugar_emision_telegramas_id', $lugarId);
    }
}