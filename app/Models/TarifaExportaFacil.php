<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaExportaFacil extends Model
{
    protected $table = 'tarifas_exporta_facil';
    protected $primaryKey='tarifa_exporta_facil_id';

    protected $fillable = [
        'pais_id',
        'monto',
        'activo',
        'medida_id',
    ];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id', 'pais_id');
    }
}
