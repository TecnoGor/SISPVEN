<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoServicioAlianza extends Model
{
    protected $table = 'catalogo_servicios_alianza';
    protected $primaryKey = 'catalogo_servicio_alianza_id';
    protected $fillable = ['nombre', 'activo'];


    use HasFactory;
}
