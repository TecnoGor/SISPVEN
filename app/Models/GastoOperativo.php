<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GastoOperativo extends Model
{
    protected $table = 'gastos_operativos';
    protected $primaryKey = 'gastos_operativos_id';
    protected $fillable = ['oficina_id', 'usuario_id', 'tipo_gasto_operativo_id', 'monto'];


    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }

    public function tipo_gasto()
    {
        return $this->belongsTo(TipoGastoOperativo::class, 'tipo_gasto_operativo_id');
    }


    use HasFactory;
}
