<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlmacenAviso extends Model
{
    protected $table= 'almacen_avisos';
    protected $primaryKey= 'almacen_aviso_id';
    protected $fillable= ['envio_almacen_id', 'envio_id', 'usuario_id'];
    
    public function almacen()
    {
        return $this->belongsTo(EnvioAlmacen::class, 'envio_almacen_id');
    }

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    }

    use HasFactory;
}
