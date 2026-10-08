<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioInternacional extends Model
{
    protected $table = 'envios_internacionales';
    protected $primaryKey = 'envio_internacional_id';
    protected $fillable = ['envio_id', 'pais', 'pais_id', 'tipo_envio', 'lista_correo', 'created_at', 'updated_at'];

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    } 


    use HasFactory;
}
