<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'paises';
    protected $primaryKey = 'pais_id';
    protected $fillable = ['nombre', 'codigo', 'continente_id'];
    protected $casts = [
        'exporta_facil' => 'boolean',
    ];
    public function continentes()
    {
        return $this->belongsTo(Continente::class, 'continente_id');
    }

    public function estados()
    {
        return $this->hasMany(Estado::class, 'pais_id');
    }

    public function tarifa()
    {
        return $this->hasOne(TarifaExportaFacil::class, 'pais_id', 'pais_id');
    }
}
