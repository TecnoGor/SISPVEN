<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespaldoInventarioDiario extends Model
{
    protected $table = 'respaldo_inventario_diario';
    protected $primaryKey = 'respaldo_inventario_diario_id';
    protected $fillable = [
        'oficina_id',
        'insumo_id',
        'cantidad',
        'fecha',
        'coste',
    ];

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id', 'insumo_id');
    }

    use HasFactory;
}
