<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rutas extends Model
{
    use HasFactory;

    protected $table = 'rutas';
    protected $primaryKey = 'ruta_id';

    // Definimos los campos que se pueden asignar en masa
    protected $fillable = [
        'distancia',
        'ruta',
        'plataforma_origen',
        'plataforma_destino',
        'tiempo',
        'activo',
    ];

    // Cast para el campo 'activo' para tratarlo como booleano
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'proveedor_ruta', 'ruta_id', 'proveedor_id');
    }
}