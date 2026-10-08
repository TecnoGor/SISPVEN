<?php

namespace App\Models;

use App\Livewire\Oficinas\Roles;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OficinaPersonal extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'oficina_personal';

    // Clave primaria personalizada
    protected $primaryKey = 'oficina_personal_id';

    // Campos asignables
    protected $fillable = [
        'oficina_id',
        'rol_id',
        'cantidad_max',
    ];

    /**
     * Relación con la oficina.
     */
    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    /**
     * Relación con el rol.
     */
    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id', 'id');
    }
}
