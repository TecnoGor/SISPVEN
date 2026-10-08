<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Comunicado extends Model
{
    protected $table = 'comunicados';

    protected $primaryKey = 'comunicado_id';

    protected $fillable = [
        'codigo',
        'tipo',
        'asunto',
        'prioridad',
        'remitente_id',
        'respuesta_comunicado_id',
        'datos_json',
        'fecha_limite',
        'seguimiento',
        'correcciones'
    ];

    protected $casts = [
        'datos_json' => 'array',
        'fecha_limite' => 'datetime',
        'seguimiento' => 'boolean',
        'correcciones' => 'integer',
    ];

    public function remitente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remitente_id');
    }

    public function respuesta_a(): BelongsTo
    {
        return $this->belongsTo(Comunicado::class, 'respuesta_comunicado_id', 'comunicado_id');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(Comunicado::class, 'respuesta_comunicado_id', 'comunicado_id');
    }

    public function destinatarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comunicado_destinatario', 'comunicado_id', 'usuario_id')
                    ->withPivot('estatus_id')
                    ->withTimestamps();
    }

    public function destinatarios_pivot(): HasMany
    {
        return $this->hasMany(ComunicadoDestinatario::class, 'comunicado_id', 'comunicado_id');
    }

    public function adjuntos(): HasMany
    {
        return $this->hasMany(ComunicadoAdjunto::class, 'comunicado_id', 'comunicado_id');
    }

    public function toComponentArray(?int $para_usuario_id = null): array
    {
        $data = $this->datos_json ?? [];

        $status_name = 'Pendiente';
        $status_color = 'bg-red-100 text-red-800 border-red-200';
        $estatus_id = 1;
        $emisor_id = null;
        $motivo_id = null;

        // Si se pasa un usuario_id, buscamos su estatus específico en la tabla pivot
        if ($para_usuario_id) {
            if ($para_usuario_id === $this->remitente_id) {
                // El usuario es el REMITENTE. Mostramos el estatus más reciente del flujo
                // (último movimiento en comunicado_destinatario).
                $ultimo_pivot = $this->destinatarios_pivot()->latest('id')->first();
                if ($ultimo_pivot && $ultimo_pivot->estatus) {
                    $status_name = $ultimo_pivot->estatus->nombre;
                    $status_color = $ultimo_pivot->estatus->color_badge;
                    $estatus_id = $ultimo_pivot->estatus_id;
                    $motivo_id = $ultimo_pivot->motivo_id;
                }
                // Si el remitente también tiene pivote propio (e.g. devolución), recuperar emisor_id
                $pivot_propio = ComunicadoDestinatario::where('comunicado_id', $this->comunicado_id)
                    ->where('usuario_id', $para_usuario_id)
                    ->latest('id')
                    ->first();
                if ($pivot_propio) {
                    $emisor_id = $pivot_propio->emisor_id;
                }
            } else {
                // El usuario es destinatario, usamos su estatus
                $pivot = ComunicadoDestinatario::where('comunicado_id', $this->comunicado_id)
                    ->where('usuario_id', $para_usuario_id)
                    ->latest('id')
                    ->first();

                if ($pivot) {
                    $estatus = $pivot->estatus;
                    if ($estatus) {
                        $status_name = $estatus->nombre;
                        $status_color = $estatus->color_badge;
                        $estatus_id = $pivot->estatus_id;
                    }
                    // Guardar emisor_id del pivote para saber quién me envió este documento
                    $emisor_id = $pivot->emisor_id;
                    $motivo_id = $pivot->motivo_id;
                }
            }
        }

        // Helper: devuelve ['label' => 'Nombre (Rol Sistema)', 'rol' => 'Gerente']
        $rolesCorrespList = ['Presidente Correspondencia', 'Director Correspondencia', 'Gerente Correspondencia', 'Analista Correspondencia', 'Usuario Correspondencia'];
        $infoUsuario = function (?User $user) use ($rolesCorrespList): array {
            if (!$user) return ['label' => 'Desconocido', 'rol' => null];
            $roles = $user->getRoleNames();
            $rolSistema = $roles->first(fn($r) => !in_array($r, $rolesCorrespList)) ?? null;
            $rolCorresp = $roles->first(fn($r) => in_array($r, $rolesCorrespList));
            $rolCorrespCorto = $rolCorresp ? str_replace(' Correspondencia', '', $rolCorresp) : null;
            $label = $rolSistema ? $user->name . ' (' . $rolSistema . ')' : $user->name;
            return ['label' => $label, 'rol' => $rolCorrespCorto];
        };

        // Info del emisor (quien me envió este doc en este paso)
        $emisor_info = $emisor_id ? $infoUsuario(User::find($emisor_id)) : null;

        // Info del remitente original
        $remitente_info = $this->remitente ? $infoUsuario($this->remitente) : ['label' => $data['sender_name'] ?? 'Desconocido', 'rol' => null];

        // Destinatario directo: si soy destinatario, "Para" soy yo; si soy remitente, el primer destinatario
        $primer_destinatario = ComunicadoDestinatario::where('comunicado_id', $this->comunicado_id)
            ->whereNull('emisor_id')
            ->first();

        if ($para_usuario_id && $para_usuario_id !== $this->remitente_id) {
            $dest_info = $infoUsuario(User::find($para_usuario_id));
        } elseif ($para_usuario_id && $para_usuario_id === $this->remitente_id && $motivo_id == 7) {
            // El creador/remitente recibió una devolución: él es el destinatario actual
            $dest_info = $infoUsuario(User::find($para_usuario_id));
        } else {
            $dest_user = $primer_destinatario ? User::find($primer_destinatario->usuario_id) : null;
            $dest_info = $dest_user ? $infoUsuario($dest_user) : ['label' => $data['destinatario'] ?? 'Desconocido', 'rol' => null];
        }

        $destinatario_id = $primer_destinatario?->usuario_id;

        // Trazabilidad: cadena completa de movimientos desde comunicado_destinatario
        $respuestas_index = $this->respuestas()
            ->select('comunicado_id', 'codigo', 'remitente_id')
            ->get()
            ->keyBy('remitente_id');

        $trazabilidad = ComunicadoDestinatario::where('comunicado_id', $this->comunicado_id)
            ->with(['usuario', 'emisor', 'estatus'])
            ->orderBy('id')
            ->get()
            ->map(function ($p) use ($infoUsuario, $respuestas_index) {
                $paso = [
                    'emisor' => $p->emisor ? $infoUsuario($p->emisor)['label'] : null,
                    'emisor_rol' => $p->emisor ? $infoUsuario($p->emisor)['rol'] : null,
                    'destinatario' => $p->usuario ? $infoUsuario($p->usuario)['label'] : 'Desconocido',
                    'destinatario_rol' => $p->usuario ? $infoUsuario($p->usuario)['rol'] : null,
                    'estatus' => $p->estatus?->nombre ?? 'Pendiente',
                    'estatus_color' => $p->estatus?->color_badge ?? 'bg-gray-100 text-gray-800 border-gray-200',
                    'fecha' => $p->created_at?->format('d/m/Y H:i'),
                    'respuesta_codigo' => null,
                    'observacion' => $p->observacion,
                ];

                // Si el estatus es "Respondido" (4), buscar el código del comunicado respuesta
                if ($p->estatus_id == 4 && $p->usuario_id) {
                    $respuesta = $respuestas_index->get($p->usuario_id);
                    $paso['respuesta_codigo'] = $respuesta?->codigo;
                }

                return $paso;
            })
            ->toArray();

        // Sender: emisor de este paso o remitente original
        $sender_info = $emisor_info ?? $remitente_info;

        // Documento raíz: subir por la cadena de respuestas hasta el origen
        $documento_raiz_codigo = null;
        $documento_raiz_db_id = null;
        if ($this->respuesta_comunicado_id) {
            $ancestro = $this;
            while ($ancestro->respuesta_comunicado_id) {
                $ancestro = self::select('comunicado_id', 'codigo', 'respuesta_comunicado_id')
                    ->find($ancestro->respuesta_comunicado_id);
                if (!$ancestro) break;
            }
            if ($ancestro && $ancestro->comunicado_id !== $this->comunicado_id) {
                $refDirecta = optional($this->respuesta_a)->codigo;
                // Solo mostrar si el raíz es diferente al padre directo
                if ($ancestro->codigo !== $refDirecta) {
                    $documento_raiz_codigo = $ancestro->codigo;
                    $documento_raiz_db_id = $ancestro->comunicado_id;
                }
            }
        }

        return array_merge($data, [
            'id' => $this->codigo,
            'type' => $this->tipo,
            'subject' => $this->asunto,
            'priority' => $this->prioridad,
            'status' => $status_name,
            'status_color' => $status_color,
            'estatus_id' => $estatus_id,
            'sender' => $sender_info['label'],
            'sender_rol' => $sender_info['rol'],
            'sender_original' => $remitente_info['label'],
            'sender_original_rol' => $remitente_info['rol'],
            'motivo_id' => $motivo_id,
            'destinatario' => $dest_info['label'],
            'destinatario_rol' => $dest_info['rol'],
            'destinatario_id' => $destinatario_id,
            'date' => $this->created_at->format('d/m/Y'),
            'referencia' => $this->respuesta_comunicado_id ? optional($this->respuesta_a)->codigo : null,
            'documento_raiz_codigo' => $documento_raiz_codigo,
            'documento_raiz_db_id' => $documento_raiz_db_id,
            'fecha_limite' => $this->fecha_limite ? $this->fecha_limite->format('d/m/Y') : ($data['fecha_limite'] ?? null),
            'historial' => $data['historial'] ?? [],
            'trazabilidad' => $trazabilidad,
            'es_real' => true,
            'db_id' => $this->comunicado_id,
            'remitente_id' => $this->remitente_id,
            'emisor_id' => $emisor_id,
            'ya_actuo_como_emisor' => $para_usuario_id ? ComunicadoDestinatario::where('comunicado_id', $this->comunicado_id)
                ->where('emisor_id', $para_usuario_id)
                ->exists() : false,
            'respuesta_comunicado_id' => $this->respuesta_id ?? $this->respuesta_comunicado_id,
            'seguimiento' => $this->seguimiento,
            'correcciones' => $this->correcciones ?? 0,
            'adjuntos' => $this->adjuntos->map(fn($a) => [
                'id' => $a->id,
                'nombre_original' => $a->nombre_original,
                'ruta_archivo' => $a->ruta_archivo,
                'tipo_mime' => $a->tipo_mime,
                'tamano' => $a->tamano,
                'tamano_formateado' => $a->tamano_formateado,
            ])->toArray(),
        ]);
    }

    /**
     * Genera automáticamente el código de control (n_control) y el correlativo secuencial
     * para un tipo de comunicado, consultando el máximo existente en datos_json.
     *
     * @param string $tipoComunicado  Valor de la columna 'tipo' (ej: 'Circular', 'MEMORANDO')
     * @return array ['n_control' => string, 'correlativo_secuencial' => int]
     */
    public static function generateControlCode(string $tipoComunicado): array
    {
        $year = now()->year;

        // Buscar el máximo correlativo_secuencial para este tipo en el año actual
        $maxCorrelativo = self::whereRaw("datos_json->>'tipo_comunicado_control' = ?", [$tipoComunicado])
            ->whereRaw("EXTRACT(YEAR FROM created_at) = ?", [$year])
            ->selectRaw("MAX((datos_json->>'correlativo_secuencial')::int) as max_corr")
            ->value('max_corr');

        // Si no se encontró con tipo_comunicado_control, intentar con la columna tipo
        if (is_null($maxCorrelativo)) {
            $maxCorrelativo = self::where('tipo', $tipoComunicado)
                ->whereRaw("EXTRACT(YEAR FROM created_at) = ?", [$year])
                ->whereNotNull('datos_json')
                ->selectRaw("MAX((datos_json->>'correlativo_secuencial')::int) as max_corr")
                ->value('max_corr');
        }

        $siguiente = ($maxCorrelativo ?? 0) + 1;

        $formatCode = function (int $seq) use ($tipoComunicado, $year) {
            return match ($tipoComunicado) {
                'Circular' => 'CIR-' . str_pad($seq, 4, '0', STR_PAD_LEFT) . '-' . $year,

                'Agenda al Decisor' => 'AD-' . str_pad($seq, 3, '0', STR_PAD_LEFT) . '-' . $year,

                'MEMORANDO' => 'MEM-' . str_pad($seq, 4, '0', STR_PAD_LEFT) . '-' . $year,

                'Minuta Horizontal' => str_pad($seq, 3, '0', STR_PAD_LEFT) . '-' . $year,

                'Punto de Cuenta - Presidencia IPOSTEL' => 'PCP Nº ' . str_pad($seq, 3, '0', STR_PAD_LEFT) . '-' . $year,

                'Punto-de-Cuenta-MPPT' => 'PC-MPPT-DM-CE-' . str_pad($seq, 3, '0', STR_PAD_LEFT) . '-' . $year,

                'Oficio - Tipo Carta' => 'IPOSTEL – P. N° OTC-' . str_pad($seq, 4, '0', STR_PAD_LEFT) . '-' . $year,

                'Oficio - Tipo Oficio' => 'MPPT/OTO/Nº' . str_pad($seq, 3, '0', STR_PAD_LEFT) . '-' . $year,

                'Punto de Información - Presidencia IPOSTEL' => 'PI Nº ' . str_pad($seq, 3, '0', STR_PAD_LEFT) . '-' . $year,

                'Punto-de-Informacion-MPPT' => 'PI-MPPT-DM-CE-' . str_pad($seq, 3, '0', STR_PAD_LEFT) . '-' . $year,

                'Punto de Cuenta - Directorio' => 'PCD Nº ' . str_pad($seq, 3, '0', STR_PAD_LEFT) . '-' . $year,

                default => 'DOC-' . str_pad($seq, 4, '0', STR_PAD_LEFT) . '-' . $year,
            };
        };

        // Verificar unicidad contra la columna codigo y avanzar si ya existe
        $nControl = $formatCode($siguiente);
        while (self::where('codigo', $nControl)->exists()) {
            $siguiente++;
            $nControl = $formatCode($siguiente);
        }

        return [
            'n_control' => $nControl,
            'correlativo_secuencial' => $siguiente,
            'tipo_comunicado_control' => $tipoComunicado,
        ];
    }
}
