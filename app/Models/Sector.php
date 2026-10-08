<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $table = 'sectores';
    protected $primaryKey = 'sector_id';
    protected $fillable = ['nombre', 'activo', 'estado_id', 'municipio_id', 'parroquia_id', 'codigo_postal_id', 'created_at', 'updated_at'];

    public function estados()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function municipios()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function parroquias()
    {
        return $this->belongsTo(Parroquia::class, 'parroquia_id');
    }

    public function codigos_postales()
    {
        return $this->belongsTo(CodigoPostal::class, 'codigo_postal_id');
    }
}
