<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsignacionComunicado extends Model
{
    protected $table = 'asignaciones_comunicados';

    protected $fillable = [
        'codigo',
        'emisor_id',
        'analista_id',
        'asunto_instruccion',
        'detalle_instruccion',
        'tipo_documento_esperado',
        'fecha_limite',
        'estatus',
        'comunicado_generado_id',
    ];

    protected $casts = [
        'fecha_limite' => 'datetime',
    ];

    public function emisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emisor_id');
    }

    public function analista(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analista_id');
    }

    public function comunicadoGenerado(): BelongsTo
    {
        return $this->belongsTo(Comunicado::class, 'comunicado_generado_id', 'comunicado_id');
    }

    public static function generarCodigo(): string
    {
        $year = now()->year;

        $maxCorrelativo = self::whereRaw("EXTRACT(YEAR FROM created_at) = ?", [$year])
            ->selectRaw("MAX(CAST(SUBSTRING(codigo FROM 'INST-([0-9]+)-') AS INTEGER)) as max_corr")
            ->value('max_corr');

        $siguiente = ($maxCorrelativo ?? 0) + 1;

        $formatCode = fn(int $seq) => 'INST-' . str_pad($seq, 3, '0', STR_PAD_LEFT) . '-' . $year;

        $codigo = $formatCode($siguiente);
        while (self::where('codigo', $codigo)->exists()) {
            $siguiente++;
            $codigo = $formatCode($siguiente);
        }

        return $codigo;
    }
}
