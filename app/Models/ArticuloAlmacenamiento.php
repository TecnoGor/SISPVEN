<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticuloAlmacenamiento extends Model
{
    protected $table= 'articulos_almacenamiento';
    protected $primaryKey= 'articulo_almacenamiento_id';
    protected $fillable= ['contrato_almacenamiento_id', 'articulo', 'cantidad'];


    public function almacenamiento()
    {
        return $this->belongsTo(ContratoAlmacenamiento::class, 'contrato_almacenamiento_id');
    }


    use HasFactory;
}
