<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoOficina extends Model
{
    use HasFactory;
    protected $table = 'productos_oficinas';
    protected $primaryKey = 'producto_oficina_id';
    public $incrementing = true;
    public $timestamps = false;
    protected $fillable = [
        'oficina_id',
        'producto_id',
        'cantidad',
    ];

    public function oficinas()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function productos()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }
}
