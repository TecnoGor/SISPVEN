<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroEntrega extends Model
{
    use HasFactory;

    protected $table = 'registros_entregas';

    protected $primaryKey = 'registro_entrega_id';

    protected $fillable = [
        'usuario_id',
        'oficina_id',
        'envio_id',
        'servicio_id',
        'nacional?',
        'codigo_envio',
        'cedula_remitente',
        'nombre_remitente',
        'costo_total',
        'coste_aviso',
        'coste_almacenaje',
        'autorizado',
        'tipo_cobro_extra',
        'monto_cobro_extra',
        'lista_correo',
        'dias_almacenaje',
        'coste_administrativo',
        'coste_presentacion_aduana',
    ];

    protected $casts = [
        'autorizado' => 'boolean',
    ];

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function servicio()
    {
        return $this->belongsTo(servicio::class, 'servicio_id');
    }

    // Relación con Envío
    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id', 'envio_id');
    }

    public function facturacion_destinatario()
    {
        return $this->hasMany(FacturacionDestinatario::class, 'registro_entrega_id');
    }
}
