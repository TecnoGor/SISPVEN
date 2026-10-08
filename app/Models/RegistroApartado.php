<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroApartado extends Model
{
    protected $table = 'registros_apartados';
    protected $primaryKey = 'registro_apartado_id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'servicio_id',
        'oficina_id',
        'tipo_documento',
        'documento',
        'nombre',
        'apellido',
        'codigo_postal',
        'telefono',
        'correo',
        'codigo_apartado_id',
        'activo',
        'usuario_id',
        'coste'
    ];

    public function apartado()
    {
        return $this->belongsTo(CodigoApartadoPostal::class, 'codigo_apartado_id');
    }

    public function servicios()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function codigos_postales()
    {
        return $this->hasOne(CodigoPostal::class, 'codigo_postal');
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }

    public function beneficiarios()
    {
        return $this->hasMany(ApartadoPostalBeneficiario::class, 'registro_apartado_id');
    }

}
