<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'ciudades';
    protected $primaryKey = 'ciudad_id';
    protected $fillable = ['nombre', 'estado_id', 'municipio_id', 'create_at'];


    public function estados()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function municipios()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function clientes_corporativos_direcciones()
    {
        return $this->hasMany(ClienteCorporativoDirecciones::class, 'ciudad_id');
    }
}
