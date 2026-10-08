<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAlianza extends Model
{
    protected $table = 'tipos_alianzas';
    protected $primaryKey = 'tipo_alianza_id';
    protected $fillable = ['nombre', 'activo'];

    use HasFactory;
}
