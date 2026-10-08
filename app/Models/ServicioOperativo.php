<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioOperativo extends Model
{
    protected $table = 'servicios_operativos';
    protected $primaryKey = 'servicio_operativo_id';
    protected $fillable = ['servicio_operativo', 'activo', 'created_at'];

    public function oficinas()
    {
        return $this->belongsToMany(Oficina::class, 'oficina_servicio_operativo', 'servicio_operativo_id', 'oficina_id');
    }
}
