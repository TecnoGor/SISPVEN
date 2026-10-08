<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturacionDetalle extends Model
{
    protected $table = 'facturacion_detalles';
    protected $primaryKey = 'facturacion_detalle_id';

    protected $fillable = [
        'servicio_id',
        'facturacion_id',
        'monto',
    ];

    public function facturacion_detalles()
    {
       return $this->belongsTo(Facturacion::class, 'facturacion_id');
    }

    public function servicios()
    {
       return $this->belongsTo(Servicio::class, 'servicio_id');
    }
}
