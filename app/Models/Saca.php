<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saca extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'sacas';

    // Clave primaria personalizada
    protected $primaryKey = 'saca_id';

    // Campos asignables en masa
    protected $fillable = [
        'tipo_saca_id',
        'oficina_id',
        'usuario_id',
        'oficina_destino_id',
        'peso',
        'codigo_saca',
        'cerrado',
        'numero_precinto',
        'numero_despacho_id',
    ];

    // Relación con el modelo TipoSaca
    public function tipoSaca()
    {
        return $this->belongsTo(TipoSaca::class, 'tipo_saca_id', 'tipo_saca_id');
    }

    // Relación con el modelo Oficina (oficina origen)
    public function oficinaOrigen()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    // Relación con el modelo Oficina (oficina destino)
    public function oficinaDestino()
    {
        return $this->belongsTo(Oficina::class, 'oficina_destino_id', 'oficina_id');
    }

    // Relación con el modelo User
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }

    public function almacen()
    {
        return $this->hasMany(EnvioAlmacen::class, 'saca_id');
    }

    public function numeroDespacho()
    {
        return $this->belongsTo(NumeroDespachoOficina::class, 'numero_despacho_id');
    }

    // Relación con el modelo Envio a través de la tabla pivote envio_saca
    public function envios()
    {
        return $this->belongsToMany(Envio::class, 'envio_saca', 'saca_id', 'envio_id');
    }

    // Historial de encaminamiento de la valija (cada salto entre oficinas).
    public function encaminamientos()
    {
        return $this->hasMany(SacaEncaminamiento::class, 'saca_id', 'saca_id');
    }

    // Último movimiento registrado de la valija (define su ubicación actual).
    public function encaminamientoActual()
    {
        return $this->hasOne(SacaEncaminamiento::class, 'saca_id', 'saca_id')
            ->latestOfMany('saca_encaminamiento_id');
    }

    // IDs del catálogo sacas_estatus.
    const ESTATUS_CREADA     = 1;
    const ESTATUS_TRANSITO   = 2;
    const ESTATUS_RECIBIDA   = 3;  // recibida en un COP: sigue cerrada y puede reexpedirse
    const ESTATUS_ABIERTA    = 4;  // recibida en una OPT: se abrió y liberó sus envíos (estado final)

    /**
     * Tipos de oficina que REEXPIDEN la valija en vez de abrirla.
     *
     * Una valija que llega a un COP (o centralizadora) sigue cerrada y puede
     * salir de nuevo hacia su destino. Cualquier otro tipo de oficina la abre,
     * libera sus envíos y la valija ya no vuelve a usarse.
     *
     * Las centralizadoras (tipo 5) hoy no están en uso, pero operativamente son
     * COP, así que se incluyen para que se comporten bien si se reactivan.
     */
    const TIPOS_OFICINA_REEXPIDEN = [4, 5];

    /**
     * Estatus en los que la valija está físicamente en la oficina del registro
     * y puede operarse. Excluye el tránsito (no está en ninguna oficina) y la
     * apertura (ya se abrió y no se vuelve a usar).
     */
    const ESTATUS_OPERABLES = [self::ESTATUS_CREADA, self::ESTATUS_RECIBIDA];

    /**
     * Determina qué estatus corresponde al recibir la valija en una oficina:
     * RECIBIDA si esa oficina reexpide, ABIERTA si la abre.
     */
    public static function estatusAlRecibirEn(?int $tipoOficinaId): int
    {
        return in_array($tipoOficinaId, self::TIPOS_OFICINA_REEXPIDEN, true)
            ? self::ESTATUS_RECIBIDA
            : self::ESTATUS_ABIERTA;
    }

    /**
     * Filtra las valijas que están FÍSICAMENTE en una oficina y pueden operarse.
     *
     * La ubicación se deriva del último movimiento de `sacas_encaminamiento`,
     * resuelto en SQL con un JOIN LATERAL porque los listados paginan y no se
     * puede traer la colección a PHP para filtrarla.
     *
     * Quedan fuera:
     *   - Las EN TRÁNSITO: en ese registro `oficina_id` es la oficina que
     *     DESPACHÓ (verificado contra datos reales), no donde está la valija.
     *     Sin excluirlas, el origen seguiría viendo una valija que ya despachó.
     *   - Las ABIERTAS: llegaron a una OPT, liberaron sus envíos y no se
     *     vuelven a usar.
     *
     * El orden por PK es fiable: la columna es autoincremental, así que el
     * mayor id es siempre el último movimiento aunque dos compartan created_at.
     */
    public function scopeUbicadaEn($query, $oficinaId)
    {
        return $query->whereIn('sacas.saca_id', function ($sub) use ($oficinaId) {
            $sub->select('se.saca_id')
                ->from('sacas_encaminamiento as se')
                ->whereRaw('se.saca_encaminamiento_id = (
                    select max(se2.saca_encaminamiento_id)
                    from sacas_encaminamiento se2
                    where se2.saca_id = se.saca_id
                )')
                ->where('se.oficina_id', $oficinaId)
                ->whereIn('se.saca_estatus_id', self::ESTATUS_OPERABLES);
        });
    }

    /**
     * Determina la ubicación actual de la valija a partir de su último
     * movimiento de encaminamiento. Funciona aunque la valija no tenga envíos.
     *
     * Devuelve: [
     *   'estado'  => 'origen' | 'transito' | 'recibida' | 'abierta',
     *   'oficina' => Oficina|null,   // dónde está (o de salida si en tránsito)
     *   'destino' => Oficina|null,   // solo cuando está en tránsito
     * ]
     */
    public function ubicacionActual(): array
    {
        $ultimo = $this->encaminamientoActual;

        // Nunca se movió: sigue en la oficina donde se creó.
        if (!$ultimo) {
            return [
                'estado'  => 'origen',
                'oficina' => $this->oficinaOrigen,
                'destino' => null,
            ];
        }

        $estatus = (int) $ultimo->saca_estatus_id;

        // En tránsito: despachada pero aún no recibida.
        // oficina_id = la oficina que la despachó (verificado contra datos reales);
        // oficina_externa_id = hacia dónde va.
        if ($estatus === self::ESTATUS_TRANSITO) {
            return [
                'estado'  => 'transito',
                'oficina' => $ultimo->oficina,        // oficina que la despachó (donde se marcó)
                'destino' => $ultimo->oficinaExterna, // hacia dónde va
            ];
        }

        // Abierta: llegó a una OPT, se abrió y liberó sus envíos. Está en esa
        // oficina pero ya no se opera — no se puede reexpedir ni reabrir.
        if ($estatus === self::ESTATUS_ABIERTA) {
            return [
                'estado'  => 'abierta',
                'oficina' => $ultimo->oficina,
                'destino' => null,
            ];
        }

        // Recibida en un COP: está en esa oficina, cerrada y lista para reexpedirse.
        return [
            'estado'  => 'recibida',
            'oficina' => $ultimo->oficina,
            'destino' => null,
        ];
    }
}
