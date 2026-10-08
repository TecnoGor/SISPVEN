<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormacionAcademica extends Model
{
    protected $table = 'formacion_academica';
    protected $primaryKey = 'formacion_academica_id';
    protected $fillable = [
        'empleado_id',
        'nivel_educativo_id',
        'carrera_id',
        'carrera_otro',
        'institucion_id',
        'institucion_otro',
        'pais_id',
        'año_graduacion',
        'titulo_obtenido',
    ];

    public function nivelEducativo()
    {
        return $this->belongsTo(NivelEducativo::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function institucion()
    {
        return $this->belongsTo(Institucion::class);
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }

    use HasFactory;
}
