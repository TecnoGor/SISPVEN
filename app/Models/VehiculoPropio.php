<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiculoPropio extends Model
{
    use HasFactory;

    // Nombre de la tabla (en caso de no seguir la convención de Laravel)
    protected $table = 'vehiculos_propios';

    // Llave primaria personalizada
    protected $primaryKey = 'vehiculo_propio_id';

    // Definir si la clave primaria es autoincremental
    public $incrementing = true;

    // Tipo de clave primaria
    protected $keyType = 'int';

    // Campos que se pueden asignar en masa
    protected $fillable = [
        'id_user',
        'placa',
        'color',
        'marca',
        'modelo',
        'año',
        'numero_poliza',
        'fecha_vencimiento',
        'capacidad_carga',
        'oficina_id',
        'activo',
    ];

    // Relación con el usuario propietario del vehículo
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relación con la oficina a la que pertenece el vehículo
    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }
}
