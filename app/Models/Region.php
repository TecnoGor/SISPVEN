<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regiones';
    protected $primaryKey = 'region_id';
    protected $fillable = ['nombre', 'activo', 'pais_id', 'created_at', 'updated_at'];

    public function paises()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function estados()
    {
        return $this->hasMany(Estado::class, 'region_id', 'region_id');
    }
}
