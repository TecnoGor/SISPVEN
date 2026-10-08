<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionContrato extends Model
{
    protected $table = 'facturaciones_contratos';
    protected $primaryKey = 'facturacion_contrato_id';

    protected $fillable = [
        'oficina_id',
        'usuario_id',
        'contrato_corporativo_id',
        'contrato_almacenamiento_id',
        'contrato_corporativo_detalle_id',
        'tipo_pago_id',
        'monto',
        'referencia',
        'tasa_aplicada',
        'monto_divisa',
    ];

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }

    public function contrato()
    {
        return $this->belongsTo(ContratoCorporativo::class, 'contrato_corporativo_id');
    }

    public function detalle()
    {
        return $this->belongsTo(ContratoCorporativoDetalle::class, 'contrato_corporativo_detalle_id');
    }

    public function pagos()
    {
        return $this->belongsTo(TipoPago::class, 'tipo_pago_id');
    }

    public function almacenamiento()
    {
        return $this->belongsTo(ContratoAlmacenamiento::class, 'contrato_almacenamiento_id');
    }

    use HasFactory;
}
