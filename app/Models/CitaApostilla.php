<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitaApostilla extends Model
{
    protected $table= 'citas_apostillas';
    protected $primaryKey= 'cita_apostilla_id';
    protected $fillable= ['estatus', 'coste', 'oficina_id', 'usuario_id', 'tipo_documento', 'documento', 'nombre', 'apellido', 'correo', 'telefono'];
    
    public function apostilla()
    {
        return $this->hasMany(Apostillas::class, 'cita_apostilla_id', 'cita_apostilla_id');
    }


    use HasFactory;
}
