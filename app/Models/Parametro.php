<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    use HasFactory;

    protected $table = 'parametro';
    protected $primaryKey = 'parametro_id';

    protected $fillable = [
        'nombre',
        'valor',
        'activo',
    ];

    public function parametroHistorico()
    {
        return $this->hasMany(ParametroHistorico::class, 'parametro_id');
    }

    public static function tasaVigenteEnFecha($parametro_id, $fecha)
    {
        $registro = ParametroHistorico::where('parametro_id', $parametro_id)
            ->where('fecha_cambio', '<=', $fecha)
            ->orderBy('fecha_cambio', 'desc')
            ->first();

        return $registro?->valor_nuevo;
    }
}
