<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Continente extends Model
{
    protected $primaryKey = 'continente_id';
    protected $fillable = ['nombre', 'codigo', 'grupo', 'activo'];

    public function pais()
    {
        return $this->hasMany(Pais::class, 'continente_id');
    }
}
