<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteCorporativoAutorizado extends Model
{
    protected $table = 'cliente_corporativo_autorizados';
    protected $primaryKey = 'cliente_corporativo_autorizado_id';
    protected $fillable = ['cliente_corporativo_id', 'activo', 'nombre', 'tipo_documento', 'documento',
    'cargo','correo', 'telefono'];

    public function corporativo()
    {
        return $this->belongsTo(ClienteCorporativo::class, 'cliente_corporativo_id', 'cliente_corporativo_id');
    }


    use HasFactory;
}
