<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioInsumo extends Model
{
    protected $table = 'insumos_inventario';
    protected $primaryKey = 'insumo_inventario_id';

    protected $fillable = ['oficina_id', 'insumo_id', 'cantidad'];


    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id', 'insumo_id');
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }


    // public function insumos()
    // {
    //     return $this->belongsToMany(Insumo::class, 'insumos_inventario', 'insumo_inventario_id', 'insumo_id')->withPivot('cantidad');
    // }


    use HasFactory;
}
