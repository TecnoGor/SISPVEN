<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlmacenAduana extends Model
{
    use HasFactory;

    protected $table = 'almacen_aduana';

    protected $primaryKey = 'almacen_aduana_id';

    protected $fillable = [
        'oficina_id',
        'envio_id',
        'codigo',
        'estatus',
        'Entrada',
        'Salida',
        'usuario_ingreso_id',
        'usuario_salida_id',
        'observaciones',
    ];

    protected $casts = [
        'estatus' => 'boolean',
        'Entrada' => 'date',
        'Salida' => 'date',
    ];

    /**
     * Relaciones (opcionalmente puedes definirlas aquí)
     */
    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    }

    public function usuarioIngreso()
    {
        return $this->belongsTo(User::class, 'usuario_ingreso_id', 'id');
    }

    public function usuarioSalida()
    {
        return $this->belongsTo(User::class, 'usuario_salida_id', 'id');
    }
}
