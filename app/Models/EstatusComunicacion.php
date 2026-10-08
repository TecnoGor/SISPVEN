<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstatusComunicacion extends Model
{
    protected $table = 'estatus_comunicaciones';

    protected $fillable = [
        'nombre',
        'color_badge',
    ];

    public $timestamps = false;
}
