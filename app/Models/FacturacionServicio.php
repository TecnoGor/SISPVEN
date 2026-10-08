<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionServicio extends Model
{
    use HasFactory;

    protected $table = 'facturacion_servicios';
    protected $primaryKey = 'facturacion_servicio_id';

    protected $fillable = [
        'servicio_id',
        'referencia_id',
        'tipo_documento',
        'documento',
        'monto_subtotal',
        'monto_iva',
        'monto_total',
        'oficina_id',
        'usuario_id',
    ];

    public function pagos()
    {
        return $this->hasMany(FacturacionServicioPago::class, 'facturacion_servicio_id');
    }
}
