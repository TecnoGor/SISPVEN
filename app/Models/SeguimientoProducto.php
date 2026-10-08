<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeguimientoProducto extends Model
{
    use HasFactory;
    protected $table = 'seguimientos_productos';
    protected $primaryKey = 'seguimiento_producto_id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'user_id',
        'oficina_id',
        'producto_id',
        'operacion',
        'cantidad_registrada',
    ];

    public function oficinas()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function usuarios()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function productos()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'producto_id');
    }

}
