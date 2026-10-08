<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OficinaServicioOperativo extends Pivot
{
    protected $table = 'oficina_servicio_operativo';
    protected $primarykey = 'oficina_servicio_operativo_id';
    protected $fillable = ['oficina_id','servicio_operativo_id','created_id','updated_id'];

    public $timestamps = true;
}
