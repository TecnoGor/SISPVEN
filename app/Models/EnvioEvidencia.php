<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioEvidencia extends Model
{
    use HasFactory;

    protected $table = 'envio_evidencias';
    protected $primaryKey = 'evidencia_id';

    protected $fillable = [
        'envio_id',
        'envio_almacen_id',
        'user_id',
        'tipo',
        'ruta_archivo',
        'hash_sha256',
        'transaction_id',
        'latitud',
        'longitud',
        'precision_gps',
        'capturada_en',
    ];

    protected $casts = [
        'latitud' => 'float',
        'longitud' => 'float',
        'precision_gps' => 'float',
        'capturada_en' => 'datetime',
    ];

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id', 'envio_id');
    }

    public function envioAlmacen()
    {
        return $this->belongsTo(EnvioAlmacen::class, 'envio_almacen_id', 'envio_almacen_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
