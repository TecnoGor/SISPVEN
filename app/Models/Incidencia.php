<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    protected $table= "incidencias";
    protected $primaryKey = 'incidencia_id';

    protected $fillable = [
        'incidencia',
    ];

    public function incidencia_detalle(){
        return $this->hasMany(IncidenciaDetalle::class, 'incidencia_id');
    }
}
