<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioSaca extends Model
{
    use HasFactory;

    protected $table = 'envio_saca';
    protected $primaryKey = 'envio_saca_id';
    protected $fillable = [
        'envio_id',
        'saca_id',
        'activo',
    ];

    public function sacas()
    {
        return $this->belongsTo(Saca::class, 'saca_id');
    }

    public function envios()
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    }
    
}
