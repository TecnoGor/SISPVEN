<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioInsumo extends Model
{
    protected $table = 'envios_insumos';
    protected $primaryKey = 'envio_insumo_id';

    protected $fillable = ['envio_id', 'insumo_id', 'coste'];


    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id', 'envio_id');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id', 'insumo_id');
    }
    
    use HasFactory;
}
