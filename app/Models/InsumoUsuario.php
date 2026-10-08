<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsumoUsuario extends Model
{
    protected $table = 'insumos_usuario';
    protected $primaryKey = 'insumo_usuario_id';
    protected $fillable = ['oficina_id','usuario_id', 'insumo_id', 'cantidad'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id', 'insumo_id');
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    use HasFactory;
}
