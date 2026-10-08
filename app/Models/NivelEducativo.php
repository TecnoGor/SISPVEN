<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelEducativo extends Model
{
    protected $table = 'niveles_educativos';
    protected $primaryKey = 'nivel_educativo_id';

    protected $fillable = [
        'nombre',
    ];

    use HasFactory;
}
