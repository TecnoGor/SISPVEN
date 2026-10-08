<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumeroDespachoOficina extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'numeros_despacho_oficina';

    // Clave primaria personalizada
    protected $primaryKey = 'numero_despacho_id';

    // Campos que se pueden asignar en masa
    protected $fillable = [
        'oficina_id',
        'oficina_destino_id',
        'numero_despacho',
        'activo',
    ];

    // Relación: un número de despacho pertenece a una oficina
    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function oficinaDestino()
    {
        return $this->belongsTo(Oficina::class, 'oficina_destino_id', 'oficina_id');
    }

    /** Valijas agrupadas en este despacho. */
    public function sacas()
    {
        return $this->hasMany(Saca::class, 'numero_despacho_id', 'numero_despacho_id');
    }

    /**
     * Envios al descubierto (sin valija) asociados al despacho. Un despacho puede
     * llevar solo sueltos, sin ninguna valija, asi que ambas relaciones hacen
     * falta para saber si tiene contenido.
     */
    public function enviosDescubiertos()
    {
        return $this->hasMany(EnvioDescubiertoDespacho::class, 'numero_despacho_id', 'numero_despacho_id');
    }
}
