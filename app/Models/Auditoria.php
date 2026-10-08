<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'accion',
        'tabla',
        'registro_id',
        'descripcion',
        'detalles_previos',
        'detalles_nuevos',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'detalles_previos' => 'array',
        'detalles_nuevos' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function registrar($accion, $tabla, $registro_id, $descripcion, $previos = null, $nuevos = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'accion' => $accion,
            'tabla' => $tabla,
            'registro_id' => $registro_id,
            'descripcion' => $descripcion,
            'detalles_previos' => $previos,
            'detalles_nuevos' => $nuevos,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
