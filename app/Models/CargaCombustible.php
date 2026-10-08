<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargaCombustible extends Model
{
    use HasFactory;

    protected $table = 'cargas_combustible';
    protected $primaryKey = 'id';

    protected $fillable = [
        'vehiculo_id',
        'fecha',
        'litros',
        'costo_total',
        'kilometraje',
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id', 'vehiculo_id');
    }
}
