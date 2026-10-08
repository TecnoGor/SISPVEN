<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadAnalisisDevolucion extends Model
{
    protected $table = 'envios_unidad_analisis_devolucion';
    protected $primaryKey = 'envio_unidad_analisis_devolucion_id';
    protected $fillable = [
        'envio_id',
        'oficina_id',
        'usuario_ingreso_id',
        'usuario_decision_id',
        'estatus',
        'observaciones_decision',
        'decision_final',
        'fecha_ingreso',
        'fecha_decision',
    ];

    protected $casts = [
        'estatus'        => 'boolean',
        'fecha_ingreso'  => 'datetime',
        'fecha_decision' => 'datetime',
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

    public function usuarioDecision()
    {
        return $this->belongsTo(User::class, 'usuario_decision_id', 'id');
    }

    public function envioEstatus()
    {
        return $this->belongsTo(EnvioEstatus::class, 'decision_final', 'envios_estatus_id');
    }


    use HasFactory;
}
