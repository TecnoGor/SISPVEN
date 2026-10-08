<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntidadBancaria extends Model
{
    protected $table = 'entidades_bancarias';
    protected $primaryKey = 'entidad_bancaria_id';
    protected $fillable = [
        'nombre',
        'codigo',
    ];

    use HasFactory;
}
