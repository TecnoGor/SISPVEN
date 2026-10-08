<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $primaryKey = 'cliente_id';
    protected $fillable = ['nombre', 'apellido', 'tipo_documento', 'numero_documento', 'telefono',
                           'correo', 'create_at'];
}
