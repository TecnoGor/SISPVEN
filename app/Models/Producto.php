<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    protected $table = 'productos';
    protected $primaryKey = 'producto_id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'nombre',
        'activo',
    ];
    public function seguimientos()
    {
        return $this->hasMany(SeguimientoProducto::class, 'producto_id');
    }

}
