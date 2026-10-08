<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturacionDestinatario extends Model
{
    protected $table= 'facturacion_destinatario';
    protected $primaryKey= 'facturacion_destinatario_id';
    protected $fillable= ['facturacion_destinatario', 'registro_entrega_id', 'tipo_pago_id', 'envio_id', 'usuario_id', 'nombre', 'tipo_documento', 'documento', 'iva', 'monto'];
    
    public function almacen()
    {
        return $this->belongsTo(EnvioAlmacen::class, 'envio_almacen_id');
    }

    public function tipopago()
    {
        return $this->belongsTo(TipoPago::class, 'tipo_pago_id');
    }

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    }

    public function registro()
    {
        return $this->belongsTo(RegistroEntrega::class, 'registro_entrega_id');
    }


    use HasFactory;
}
