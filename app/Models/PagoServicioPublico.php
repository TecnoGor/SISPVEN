<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoServicioPublico extends Model
{
    protected $table = 'pagos_servicios_publicos';
    protected $primaryKey = 'pago_servicio_publico_id';
    protected $fillable = ['oficina_id', 'usuario_id', 'servicio_publico_id', 'mensualidad', 'monto',  'estatus'];
    protected $casts = ['mensualidad' => 'date',];

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function servicio_publico()
    {
        return $this->belongsTo(ServicioPublico::class, 'servicio_publico_id', 'servicio_publico_id');
    }

    use HasFactory;
}
