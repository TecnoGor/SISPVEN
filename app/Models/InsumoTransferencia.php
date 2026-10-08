<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsumoTransferencia extends Model
{
    protected $table = 'insumos_transferencias';
    protected $primaryKey = 'insumo_transferencia_id';

    protected $fillable = ['usuario_id', 'insumo_id', 'cantidad', 'oficina_origen', 'oficina_destino', 'coste'];

    use HasFactory;

    public function insumos()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }

    public function oficinaOrigen()
    {
        return $this->belongsTo(Oficina::class, 'oficina_origen');
    }

    public function oficinaDestino()
    {
        return $this->belongsTo(Oficina::class, 'oficina_destino');
    }
}
