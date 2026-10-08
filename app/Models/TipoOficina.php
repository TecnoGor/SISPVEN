<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoOficina extends Model
{
    protected $table = 'tipos_oficinas';
    protected $primaryKey = 'tipo_oficina_id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['nombre', 'activo', 'create_at'];


    public function oficinas()
    {
        return $this->hasMany(Oficina::class, 'oficina_id');
    }

    public function roles_tipos_oficinas()
    {
        return $this->hasMany(RoleTipoOficina::class, 'tipo_oficina_id', 'tipo_oficina_id');
    }
}
