<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametroHistorico extends Model
{
    protected $table = 'parametros_historicos';
    protected $primaryKey = 'parametro_historico_id';

    protected $fillable = [
        'parametro_id',
        'valor_anterior',
        'valor_nuevo',
        'usuario_id',
        'fecha_cambio',
    ];

    protected $casts = [
        'fecha_cambio' => 'datetime',
    ];

    public function parametro()
    {
        return $this->belongsTo(Parametro::class, 'parametro_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }


    use HasFactory;
}
