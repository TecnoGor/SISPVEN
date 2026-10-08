<?php

namespace App\Models;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleTipoOficina extends Model
{
    protected $table = 'role_tipo_oficina';
    protected $primaryKey = 'role_tipo_oficina_id';
    protected $fillable = ['rol_id', 'tipo_oficina_id', 'created_at', 'updated_at'];

    public function tipos_oficinas()
    {
        return $this->belongsTo(TipoOficina::class, 'tipo_oficina_id', 'tipo_oficina_id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'rol_id', 'id');
    }

}
