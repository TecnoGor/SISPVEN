<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartadoPostalBeneficiario extends Model
{
    protected $table = 'apartado_postal_beneficiarios';
    protected $primaryKey = 'apartado_postal_beneficiario_id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'registro_apartado_id',
        'nombre',
        'apellido',
        'tipo_documento',
        'documento',
        'correo',
        'telefono',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // El titular del apartado (registro del cliente) al que pertenece el beneficiario.
    public function titular()
    {
        return $this->belongsTo(RegistroApartado::class, 'registro_apartado_id');
    }
}
