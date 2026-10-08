<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoGastoOperativo extends Model
{
    protected $table = 'tipos_gastos_operativos';
    protected $primaryKey = 'tipo_gasto_operativo_id';
    protected $fillable = ['tipo_gasto_operativo'];

    public function gasto_operativo()
    {
        return $this->hasMany(GastoOperativo::class, 'tipo_gasto_operativo_id');
    }

    use HasFactory;
}
