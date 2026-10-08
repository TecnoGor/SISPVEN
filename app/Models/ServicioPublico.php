<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioPublico extends Model
{
    protected $table = 'servicios_publicos';
    protected $primaryKey = 'servicio_publico_id';
    protected $fillable = ['nombre', 'created_at', 'updated_at'];

    public function pagos_servicios_publicos()
    {
        return $this->hasMany(PagoServicioPublico::class, 'servicio_publico_id', 'servicio_publico_id');
    }


    use HasFactory;
}
