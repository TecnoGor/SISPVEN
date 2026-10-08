<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametro_Seguimiento extends Model
{
    use HasFactory;
    protected $table = 'parametro_seguimiento';
    protected $primaryKey='parametro_seguimiento_id';

    protected $fillable = [
        'usuario_seguimiento_id',
        'valor',
    ]; 
}
