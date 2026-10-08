<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManifiestoPaquete extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'manifiestos_paquetes';
    protected $primaryKey = 'manifiestos_paquetes_id';
    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'manifiesto_id',
        'saca_id',
        'envio_id',
        'servicio_id',
        'peso',
    ];

    /**
     * Relación con el modelo Manifiesto.
     */
    public function manifiesto()
    {
        return $this->belongsTo(Manifiesto::class, 'manifiesto_id', 'manifiesto_id');
    }
    /**
     * Relación con el modelo Saca.
     */
    public function saca()
    {
        return $this->belongsTo(Saca::class, 'saca_id', 'saca_id');
    }

    /**
     * Relación con el modelo Envio.
     */
    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id', 'envio_id');
    }

    // Relación con servicios
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id', 'servicio_id');
    }
}
