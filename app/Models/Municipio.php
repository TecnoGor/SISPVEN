<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $primaryKey = 'municipio_id';
    protected $fillable = ['nombre', 'estado_id', 'activo', 'create_at'];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function parroquias()
    {
        return $this->hasMany(Parroquia::class, 'municipio_id');
    }

    public function clientes_corporativos()
    {
        return $this->hasMany(ClienteCorporativo::class, 'municipio_id');
    }

    public function clientes_corporativos_direcciones()
    {
        return $this->hasMany(ClienteCorporativoDirecciones::class, 'municipio_id');
    }
}
