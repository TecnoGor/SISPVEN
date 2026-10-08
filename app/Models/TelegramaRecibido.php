<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramaRecibido extends Model
{
    protected $table = 'telegramas_recibidos';
    protected $primaryKey = 'telegrama_recibido_id';

    protected $fillable = ['envio_id', 'recibido', 'tipo_remitente', 'lugar_emision_rem', 'sitio_especifico_emision', 
    'circuito_judicial_dest', 'contenido_telegrama', 'palabras_tasables', 'palabras_reales'];

    public function envios()
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    }

    use HasFactory;
}
