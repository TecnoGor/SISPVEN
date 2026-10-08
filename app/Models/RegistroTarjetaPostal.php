<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroTarjetaPostal extends Model
{
    protected $table = 'registros_tarjetas_postales';
    protected $primaryKey = 'registro_tarjeta_postal_id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'servicio_id',
        'usuario_id',
        'oficina_id',
        'cantidad_tarjetas',
        'tipo_documento',
        'documento',
        'nombre',
        'apellido',
        'telefono',
        'correo',
    ];

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'usuario_id');
    }
}
