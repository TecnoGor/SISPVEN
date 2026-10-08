<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;
    protected $table = 'proveedores';
    protected $primaryKey = 'proveedor_id';
    // Si quieres permitir la asignación masiva
    protected $fillable = [
        'representante_legal',
        'telefono',
        'cedula',
        'correo',
        'rif',
        'razon_social',
        'direccion_fiscal',
        'retencion',
        'contribuyente_especial',
        'propio',
        'activo',
    ];
    public function rutas()
    {
        return $this->belongsToMany(Rutas::class, 'proveedor_ruta', 'proveedor_id', 'ruta_id');
    }
}


