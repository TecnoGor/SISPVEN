<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RutaPuntoEntrega extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'rutas_puntos_entregas';

    // Clave primaria personalizada
    protected $primaryKey = 'rutas_puntos_entregas';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'ruta_id',
        'oficina_id',
    ];

    /**
     * Relación con la tabla Rutas.
     * Una ruta punto de entrega pertenece a una ruta.
     */
    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    // Relación con la ruta
    public function ruta()
    {
        return $this->belongsTo(RutasModelo::class, 'ruta_id', 'ruta_id');
    }
}
