<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chofer extends Model
{
    use HasFactory;

    protected $table = 'choferes'; // Nombre de la tabla en la base de datos

    protected $primaryKey = 'chofer_id'; // Define la clave primaria

    protected $fillable = [
        'nombre',
        'cedula',
        'rif',
        'direccion',
        'telefono',
        'correo',
        'proveedor_id',
        'oficina_id', // Asegúrate de agregar este campo si está presente en la migración
        'activo',
    ];

    public $timestamps = true; // Indica si se deben gestionar las columnas created_at y updated_at

    // Relación con el modelo Proveedor (si aplica)
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'proveedor_id');
    }
    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }
}
