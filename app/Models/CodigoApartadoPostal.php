<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodigoApartadoPostal extends Model
{
    protected $table = 'codigos_apartados';
    protected $primaryKey = 'codigo_apartado_id';
    protected $fillable = ['apartado', 'operativo', 'oficina_id', 'activo', 'created_at', 'updated_at'];

    public function registro_apartado()
    {
        return $this->hasOne(RegistroApartado::class, 'codigo_apartado_id');
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }
}
