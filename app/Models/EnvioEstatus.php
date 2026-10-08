<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioEstatus extends Model
{
    use HasFactory;

    protected $table = 'envios_estatus';
    protected $primaryKey = 'envios_estatus_id';
    protected $fillable = [
        'estatus'
    ];

    public function envio_encaminamiento()
    {
        return $this->hasMany(EnvioEncaminamiento::class, 'estatus_id', 'envios_estatus_id');
    }
}
