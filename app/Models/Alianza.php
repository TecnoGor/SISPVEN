<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alianza extends Model
{
    protected $table= 'alianzas';
    protected $primaryKey= 'alianza_id';
    protected $fillable= ['cliente_corporativo_id', 'tipo_alianza_id', 'porcentaje', 'activo'];
    
    public function cliente()
    {
        return $this->belongsTo(ClienteCorporativo::class, 'cliente_corporativo_id', 'cliente_corporativo_id');
    }

    public function alianza()
    {
        return $this->belongsTo(TipoAlianza::class, 'tipo_alianza_id', 'tipo_alianza_id');
    }

    use HasFactory;
}
