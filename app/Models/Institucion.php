<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    protected $table = 'instituciones';
    protected $primaryKey = 'institucion_id';
    protected $fillable = [
        'nombre',
    ];

    use HasFactory;
}
