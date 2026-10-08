<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComunicadoDestinatario extends Model
{
    protected $table = 'comunicado_destinatario';

    protected $fillable = [
        'comunicado_id',
        'usuario_id',
        'emisor_id',
        'estatus_id',
        'motivo_id',
        'observacion',
    ];

    public function comunicado(): BelongsTo
    {
        return $this->belongsTo(Comunicado::class, 'comunicado_id', 'comunicado_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function emisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emisor_id');
    }

    public function estatus(): BelongsTo
    {
        return $this->belongsTo(EstatusComunicacion::class, 'estatus_id');
    }

    public function motivo(): BelongsTo
    {
        return $this->belongsTo(EstatusComunicacion::class, 'motivo_id');
    }
}
