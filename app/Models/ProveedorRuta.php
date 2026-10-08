<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProveedorRuta extends Model
{
    protected $table = 'proveedor_ruta';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = ['proveedor_id', 'ruta_id'];

    // Definir relaciones
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function ruta()
    {
        return $this->belongsTo(Rutas::class, 'ruta_id');
    }
}
