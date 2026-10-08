<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OficinaVehiculo extends Model
{
   protected $table = 'oficinas_vehiculos';
   protected $primaryKey = 'oficina_vehiculo_id';
   protected $fillable = ['oficina_id', 'vehiculo_id', 'activo']; 

   public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id', 'vehiculo_id');
    }

    use HasFactory;
}
