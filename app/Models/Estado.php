<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estados';
    protected $primaryKey = 'estado_id';
    protected $fillable = ['nombre', 'pais_id', 'region_id', 'codigo', 'activo', 'create_at'];

    /**
     * Estados secundarios administrados por el gerente de un estado principal.
     * estado_id principal => [ids secundarios cubiertos]
     *
     * Fuente de verdad compartida para determinar el ámbito territorial de un
     * Gerente de Estado (p. ej. en la asignación de rol y en las rutas locales).
     */
    public const ESTADOS_SECUNDARIOS = [
        1  => [2, 24],   // Distrito Capital → La Guaira, Miranda
        20 => [21],      // Monagas → Delta Amacuro
    ];

    /**
     * Devuelve los estado_id que cubre un estado principal (él mismo + secundarios).
     * Si el estado no tiene secundarios, devuelve solo su propio id.
     * Con $estadoId nulo devuelve un array vacío.
     */
    public static function estadosCubiertos($estadoId): array
    {
        if (!$estadoId) {
            return [];
        }

        $principal = (int) $estadoId;

        return array_merge([$principal], self::ESTADOS_SECUNDARIOS[$principal] ?? []);
    }

    /**
     * Inverso de estadosCubiertos(): devuelve el estado principal que administra
     * un estado dado. Si el estado es secundario (p. ej. Miranda), devuelve el
     * principal que lo cubre (Distrito Capital); si no, se devuelve a sí mismo.
     * Con $estadoId nulo devuelve null.
     */
    public static function estadoPrincipal($estadoId): ?int
    {
        if (!$estadoId) {
            return null;
        }

        $estadoId = (int) $estadoId;

        foreach (self::ESTADOS_SECUNDARIOS as $principal => $secundarios) {
            if (in_array($estadoId, $secundarios, true)) {
                return $principal;
            }
        }

        return $estadoId;
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'region_id');
    }

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'estado_id');
    }

    public function clientes_corporativos()
    {
        return $this->hasMany(ClienteCorporativo::class, 'estado_id');
    }

    public function usuarios()
    {
        return $this->hasMany(UsuarioAppMovil::class, 'estado_id', 'estado_id');
    }

    public function oficinas()
    {
        return $this->hasMany(Oficina::class, 'estado_id', 'estado_id');
    }

    public function clientes_corporativos_direcciones()
    {
        return $this->hasMany(ClienteCorporativoDirecciones::class, 'estado_id');
    }
}
