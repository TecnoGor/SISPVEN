<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteCorporativoDirecciones extends Model
{
    protected $table = 'clientes_corporativos_direcciones';
    protected $primaryKey = 'cliente_corporativo_direccion_id';

    protected $fillable = [
        'cliente_corporativo_id',
        'alias',
        'persona',
        'estado_id',
        'municipio_id',
        'ciudad_id',
        'parroquia_id',
        'codigo_postal',
        'direccion',
        'correo',
        'telefono',
        'activo',
    ];

    public function cliente_corporativo()
    {
        return $this->belongsTo(ClienteCorporativo::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class, 'parroquia_id');
    }


    use HasFactory;
}
