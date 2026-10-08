<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespaldoInventarioUsuario extends Model
{
    protected $table = 'respaldo_inventario_usuario';
    protected $primaryKey = 'respaldo_inventario_usuario_id';
    protected $filable = ['oficina_id', 'usuario_id', 'insumo_id', 'coste'];

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id', 'insumo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }


    use HasFactory;
}
