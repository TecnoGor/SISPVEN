<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComunicadoAdjunto extends Model
{
    protected $table = 'comunicado_adjuntos';

    protected $fillable = [
        'comunicado_id',
        'nombre_original',
        'ruta_archivo',
        'tipo_mime',
        'tamano',
    ];

    public function comunicado()
    {
        return $this->belongsTo(Comunicado::class, 'comunicado_id', 'comunicado_id');
    }

    /**
     * Retorna el tamaño formateado (KB, MB)
     */
    public function getTamanoFormateadoAttribute()
    {
        $bytes = $this->tamano;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
