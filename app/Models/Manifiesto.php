<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manifiesto extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si sigue la convención pluralizada)
    protected $table = 'manifiestos';

    // Clave primaria personalizada
    protected $primaryKey = 'manifiesto_id';

    // Tipo de clave primaria
    public $incrementing = true;
    protected $keyType = 'int';

    // Atributos asignables en masa
    protected $fillable = [
        'oficina_id',
        'oficina_destino_id',
        'status',
    ];

    // Relación con Oficina (oficina_id)
    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    // Relación con Oficina (oficina_destino_id)
    public function oficinaDestino()
    {
        return $this->belongsTo(Oficina::class, 'oficina_destino_id', 'oficina_id');
    }

    /**
     * Bultos declarados en el manifiesto. Cada fila es O una valija O un envio
     * al descubierto (saca_id y envio_id son excluyentes).
     */
    public function paquetes()
    {
        return $this->hasMany(ManifiestoPaquete::class, 'manifiesto_id', 'manifiesto_id');
    }
}
