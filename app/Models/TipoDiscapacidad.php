<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDiscapacidad extends Model
{
    protected $table = 'tipos_discapacidades';
    protected $primaryKey = 'tipo_discapacidad_id';
    protected $fillable = [
        'nombre',
    ];

    use HasFactory;
}
