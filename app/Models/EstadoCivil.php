<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoCivil extends Model
{
    protected $table = 'estados_civiles';
    protected $primaryKey = 'estado_civil_id';
    protected $fillable = [
        'nombre',
    ];


    use HasFactory;
}
