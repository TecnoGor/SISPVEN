<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntentoEntrega extends Model
{
    protected $table = 'intentos_entregas';
    protected $primaryKey = 'intento_entrega_id';
    protected $fillable = [
        'envio_almacen_id',
        'envio_id',
        'usuario_id',
        'motivo_id',
        'latitud',
        'longitud',
        'evidencia_id',
    ];

    protected $casts = [
        'latitud' => 'float',
        'longitud' => 'float',
    ];

    public function envio_almacen()
    {
        return $this->belongsTo(EnvioAlmacen::class, 'envio_almacen_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function motivo()
    {
        return $this->belongsTo(MotivoDevolucion::class, 'motivo_id');
    }

    public function evidencia()
    {
        return $this->belongsTo(EnvioEvidencia::class, 'evidencia_id', 'evidencia_id');
    }


    use HasFactory;
}
