<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarreraEstudio extends Model
{
    protected $table = 'carreras_estudio';
    protected $primaryKey = 'carrera_estudio_id';

    protected $fillable = [
        'nombre',
    ];


    use HasFactory;
}
