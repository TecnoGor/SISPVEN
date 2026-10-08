<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    protected $table = 'insumos';
    protected $primaryKey = 'insumo_id';

    protected $fillable = ['descripcion', 'costo', 'activo'];

    public function oficinas()
    {
        return $this->belongsToMany(Oficina::class, 'insumos_inventario', 'insumo_id', 'oficina_id')
            ->withPivot('cantidad')->withTimestamps();
    }

    public function envio_insumo()
    {
        return $this->hasMany(EnvioInsumo::class, 'insumo_id', 'insumo_id');
    }

    public function usuarios()
    {
        return $this->hasMany(InsumoUsuario::class, 'insumo_id', 'insumo_id');
    }

    public function insumos_usuarios_transferencias()
    {
        return $this->hasMany(InsumoUsuarioTransferencia::class, 'insumo_id', 'insumo_id');
    }

    use HasFactory;
}
