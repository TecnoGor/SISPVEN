<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documentos';
    protected $primaryKey = 'documento_id';
    protected $fillable = ['tipo', 'descripcion', 'activo', 'created_at', 'updated_at'];
}
