<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvisoTelegrama extends Model
{
    protected $table= 'avisos_telegramas';
    protected $primaryKey= 'aviso_telegrama_id';
    protected $fillable= ['oficina_id', 'envio_id', 'usuario_id'];

    public function envio_telegrama()
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    }

    use HasFactory;
}
