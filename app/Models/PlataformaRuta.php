<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlataformaRuta extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'plataforma_ruta';

    // Clave primaria personalizada
    protected $primaryKey = 'plataforma_ruta_id';

    // Campos asignables en masa
    protected $fillable = [
        'ruta_id',
        'estado_id',
        'siglas',
    ];

    // Relación con el modelo Ruta
    public function ruta()
    {
        return $this->belongsTo(RutasModelo::class, 'ruta_id', 'ruta_id');
    }

    // Relación con el modelo Estado
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id', 'estado_id');
    }
}