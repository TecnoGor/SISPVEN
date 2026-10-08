<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoFamiliar extends Model
{
    protected $table = 'grupo_familiar';
    protected $primaryKey = 'grupo_familiar_id';
    protected $fillable = [
        'usuario_id',
        'oficina_id',
        'empleado_id',
        'parentesco_id',
        'genero',
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'tipo_documento',
        'documento',
        'telefono',
        'trabaja',
        'estudia',
        'nivel_educativo_id',
        'vive_con_empleado',
        'tiene_discapacidad',
        'partida_nacimiento'
    ];

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function parentesco()
    {
        return $this->belongsTo(Parentesco::class);
    }

    public function nivelEducativo()
    {
        return $this->belongsTo(NivelEducativo::class);
    }


    use HasFactory;
}
