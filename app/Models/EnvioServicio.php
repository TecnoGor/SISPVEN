<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioServicio extends Model
{
    protected $table = 'envio_servicio';
    protected $primarykey = 'envio_servicio_id';
    protected $fillable = ['envio_id','servicio_id','created_id','updated_id'];




    public function servicios()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function envios()
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    }
}
