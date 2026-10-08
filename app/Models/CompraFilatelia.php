<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompraFilatelia extends Model
{
    protected $table = 'compras_filatelia';
    protected $primaryKey = 'compra_filatelia_id';
    protected $fillable = ['oficina_id', 'usuario_id', 'coste', 'tipo_documento', 'documento', 'nombre', 'apellido', 'telefono', 'correo'];

    use HasFactory;
}
