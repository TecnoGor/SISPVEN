<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioIposplus extends Model
{
    protected $table = 'envios_iposplus';
    protected $primaryKey = 'envio_iposplus_id';

    protected $fillable = ['envio_id', 'alto', 'largo', 'ancho'];

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    }

    use HasFactory;
}
