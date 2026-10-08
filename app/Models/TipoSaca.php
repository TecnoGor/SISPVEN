<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoSaca extends Model
{
    use HasFactory;

    protected $table = 'tipos_sacas';
    protected $primaryKey = 'tipo_saca_id';
    protected $fillable = [
        'nombre',
        'nombre_referencial',
        'servicio_id',
        'certificado',
        'activo',
        'cargar_por_peso',
    ];

    protected $casts = [
        'certificado' => 'boolean',
        'activo' => 'boolean',
        'cargar_por_peso' => 'boolean',
    ];

    public function sacas()
    {
        return $this->hasMany(Saca::class, 'tipo_saca_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id', 'servicio_id');
    }

    public function servicios()
    {
        return $this->belongsToMany(
            Servicio::class,
            'tipo_saca_servicio',
            'tipo_saca_id',
            'servicio_id'
        );
    }
}
