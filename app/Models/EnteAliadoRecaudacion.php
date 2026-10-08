<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnteAliadoRecaudacion extends Model
{
    protected $table = 'entes_aliados_recaudacion';
    protected $primaryKey = 'ente_aliado_recaudacion_id';
    protected $fillable = ['nombre', 'activo'];

    use HasFactory;
}
