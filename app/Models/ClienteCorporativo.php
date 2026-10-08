<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteCorporativo extends Model
{
    protected $table = 'clientes_corporativos';
    protected $primaryKey = 'cliente_corporativo_id';
    protected $fillable = ['tipo_documento', 'numero_documento', 'razon_social', 'agente_autorizado', 'estado_id',
    'municipio_id', 'parroquia_id', 'codigo_postal', 'latitud', 'longitud', 'telefono', 'correo', 'direccion', 'activo',
    'oficina_id', 'usuario_id', 'created_at', 'updated_at'];

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function contrato()
    {
        return $this->hasMany(ContratoCorporativo::class, 'cliente_corporativo');
    }

    public function contrato_detalles()
    {
        return $this->hasMany(ContratoCorporativoDetalle::class, 'cliente_corporativo_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class, 'parroquia_id');
    }

    public function autorizados()
    {
        return $this->hasMany(ClienteCorporativoAutorizado::class, 'cliente_corporativo_id', 'cliente_corporativo_id');
    }

    public function alianza()
    {
        return $this->hasOne(Alianza::class, 'cliente_corporativo_id', 'cliente_corporativo_id');
    }
}
