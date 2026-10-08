<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodigoPostal extends Model
{
    protected $table = 'codigos_postales';
    protected $primaryKey = 'codigo_postal_id';
    protected $fillable = ['codigo', 'activo', 'created_at', 'updated_at'];


    public function sectores()
    {
        return $this->hasMany(Sector::class, 'codigo_postal_id');
    }

    public function parroquias()
    {
        return $this->belongsToMany(Parroquia::class, 'codigo_postal_parroquia');
    }

    public function oficinas()
    {
        return $this->belongsToMany(Oficina::class, 'oficina_codigo_postal', 'codigo_postal_id', 'oficina_id');
    }
}
