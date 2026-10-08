<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nacionalidad extends Model
{
    protected $table = 'nacionalidad';
    protected $primaryKey = 'nacionalidad_id';

    protected $fillable = [
        'nombre',
    ];

    use HasFactory;
}
