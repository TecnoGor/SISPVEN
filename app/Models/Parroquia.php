<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parroquia extends Model
{
    protected $primaryKey = 'parroquia_id';
    protected $fillable = ['nombre', 'municipio_id', 'activo', 'create_at'];

    public function municipio()
    {
        return $this->belongsTo(municipio::class, 'municipio_id');
    }


    public function sectores()
    {
        return $this->hasMany(sector::class, 'parroquia_id');
    }

    public function clientes_corporativos()
    {
        return $this->hasMany(ClienteCorporativo::class, 'parroquia_id');
    }

    public function clientes_corporativos_direcciones()
    {
        return $this->hasMany(ClienteCorporativoDirecciones::class, 'parroquia_id');
    }
}
