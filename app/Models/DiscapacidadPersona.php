<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscapacidadPersona extends Model
{
    protected $table = 'discapacidades_persona';
    protected $primaryKey = 'discapacidad_persona_id';
    protected $fillable = [
        'empleado_id',
        'grupo_familiar_id',
        'tipo_discapacidad_id',
        'discapacidad_detalle',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function grupoFamiliar()
    {
        return $this->belongsTo(GrupoFamiliar::class);
    }

    public function tipoDiscapacidad()
    {
        return $this->belongsTo(TipoDiscapacidad::class);
    }

    use HasFactory;
}
