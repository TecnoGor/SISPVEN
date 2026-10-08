<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OficinaSemaforoPostal extends Model
{
    protected $table = 'oficinas_semaforo_postal';
    protected $primaryKey = 'oficina_semaforo_postal_id';
    protected $fillable = ['oficina_id', 'condicion', 'fecha_inicio', 'fecha_fin', 'create_at'];

    const CONDICION_ARRENDADA = 'Arrendada';
    const CONDICION_ENACOMODATO = 'En Comodato';
    const CONDICION_PROPIA_IPOSTEL = 'Propia Ipostel';

    public static function getCondiciones()
    {
        return [
            self::CONDICION_ARRENDADA,
            self::CONDICION_ENACOMODATO,
            self::CONDICION_PROPIA_IPOSTEL,
        ];
    }

    public function oficinas()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }
}
