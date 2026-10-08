<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoContrato extends Model
{
    protected $table = 'tipos_contratos';
    protected $primaryKey = 'tipo_contrato_id';
    protected $fillable = ['descripcion'];

    public function contrato ()
    {
        return $this->hasMany(ContratoCorporativo::class, 'tipo_contrato_id');
    }
}
