<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoAlmacenamiento extends Model
{
    protected $table = 'contratos_almacenamientos';
    protected $primaryKey = 'contrato_almacenamiento_id';
    protected $fillable = [
        'oficina_id',
        'usuario_id',
        'cliente_corporativo_id',
        'espacio',
        'fecha_inicio',
        'fecha_fin',
        'parametro_id',
        'monto_divisa',
        'tarifa',
        'activo',
        'created_at'
    ];

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function cliente()
    {
        return $this->belongsTo(ClienteCorporativo::class, 'cliente_corporativo_id');
    }

    public function detalles()
    {
        return $this->hasMany(ContratoCorporativoDetalle::class, 'contrato_almacenamiento_id');
    }

    public function divisa()
    {
        return $this->belongsTo(Parametro::class, 'parametro_id');
    }



    use HasFactory;
}
