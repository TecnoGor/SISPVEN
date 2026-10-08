<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContratoCorporativo extends Model
{
    use HasFactory;

    protected $table = 'contratos_corporativos';
    protected $primaryKey = 'contrato_corporativo_id';
    protected $fillable = [
        'cliente_corporativo_id',
        'tipo_contrato_id',
        'oficina_id',
        'usuario_id',
        'peso_contrato',
        'peso_utilizado',
        'cant_envios',
        'cant_envios_utilizados',
        'fecha_inicio',
        'fecha_fin',
        'tarifa',
        'activo',
        'parametro_id',
        'monto_divisa',
        'created_at',
        'updated_at'
    ];

    public function cliente()
    {
        return $this->belongsTo(ClienteCorporativo::class, 'cliente_corporativo_id');
    }

    public function contrato_detalles()
    {
        return $this->hasMany(ContratoCorporativoDetalle::class, 'contrato_corporativo_id');
    }

    public function tipo_contrato()
    {
        return $this->belongsTo(TipoContrato::class, 'tipo_contrato_id', 'tipo_contrato_id');
    }

    public function divisa()
    {
        return $this->belongsTo(Parametro::class, 'parametro_id', 'parametro_id');
    }
}
