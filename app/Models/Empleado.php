<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleados';
    protected $primaryKey = 'empleado_id';
    protected $fillable = [
        'usuario_id',
        'oficina_id',
        'nombre',
        'apellido',
        'tipo_documento',
        'documento',
        'correo',
        'telefono',
        'telefono_secundario',
        'telefono_emergencia',
        'fecha_nacimiento',
        'genero',
        'nacionalidad',
        'estado_civil',
        'fecha_ingreso',
    ];

    use HasFactory;

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function nacionalidad()
    {
        return $this->belongsTo(Nacionalidad::class);
    }

    public function estadoCivil()
    {
        return $this->belongsTo(EstadoCivil::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'empleado_id', 'empleado_id');
    }
}
