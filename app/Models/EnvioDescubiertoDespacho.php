<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioDescubiertoDespacho extends Model
{
    use HasFactory;

    protected $table = 'envio_descubierto_despacho';
    protected $primaryKey = 'envio_descubierto_despacho_id';

    protected $fillable = [
        'envio_id',
        'numero_despacho_id',
    ];

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id', 'envio_id');
    }

    public function numeroDespacho()
    {
        return $this->belongsTo(NumeroDespachoOficina::class, 'numero_despacho_id', 'numero_despacho_id');
    }
}
