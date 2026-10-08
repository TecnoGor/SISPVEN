<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioExpedicion extends Model
{
    protected $table = 'envios_expedicion';
    protected $primaryKey = 'envio_expedicion_id';

    protected $fillable = [
        'oficina_id',
        'envio_id',
        'estatus',
        'Entrada',
        'Salida',
        'usuario_ingreso_id',
        'usuario_salida_id',
    ];

    protected $casts = [
        'estatus' => 'boolean',
        'Entrada' => 'date',
        'Salida' => 'date',
    ];

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id', 'envio_id');
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function usuarioIngreso()
    {
        return $this->belongsTo(User::class, 'usuario_ingreso_id', 'id');
    }

    public function usuarioSalida()
    {
        return $this->belongsTo(User::class, 'usuario_salida_id', 'id');
    }

    use HasFactory;
}
