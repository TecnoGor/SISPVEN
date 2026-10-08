<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecolectaEstatus extends Model
{
    use HasFactory;

    protected $table = 'recolecta_estatus';
    protected $primaryKey = 'recolecta_estatus_id';

    protected $fillable = [
        'nombre',
        'slug',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public const SOLICITADA = 'solicitada';
    public const PAGO_REPORTADO = 'pago_reportado';
    public const PAGO_CONFIRMADO = 'pago_confirmado';
    public const RECOLECTADA = 'recolectada';
    public const CANCELADA = 'cancelada';
    public const RECHAZADA = 'rechazada';

    public static function porSlug(string $slug): self
    {
        return static::where('slug', $slug)->firstOrFail();
    }

    public function recolectas()
    {
        return $this->hasMany(Recolecta::class, 'recolecta_estatus_id');
    }
}
