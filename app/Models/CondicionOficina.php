<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CondicionOficina extends Model
{
    protected $table = 'condiciones_oficinas';
    protected $primaryKey = 'condicion_oficina_id';
    protected $fillable = [ 'condicion', 'fecha_inicio', 'fecha_finalizacion', 'create_at'];

    public function oficinas()
    {
        return $this->hasMany(Oficina::class, 'oficina_id');
    }
}
