<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioRezago extends Model
{
    use HasFactory;

    protected $table = 'envios_rezago';
    protected $primaryKey = 'envio_rezago_id';

    protected $fillable = [
        'oficina_id',
        'envio_id',
        'estatus',
        'Entrada',
        'Salida',
        'observaciones',
        'usuario_ingreso_id',
        'usuario_salida_id',
    ];

    protected $casts = [
        'estatus' => 'boolean',
        'Entrada' => 'date',
        'Salida' => 'date',
    ];

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id', 'envio_id');
    }

    public function usuarioIngreso()
    {
        return $this->belongsTo(User::class, 'usuario_ingreso_id');
    }

    public function usuarioSalida()
    {
        return $this->belongsTo(User::class, 'usuario_salida_id');
    }
}
