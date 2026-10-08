<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OficinaCodigoPostal extends Pivot
{
    protected $table = 'oficina_codigo_postal';
    protected $primarykey = 'oficina_codigo_postal_id';
    protected $fillable = ['oficina_id','codigo_postal_id','created_id','updated_id'];

    public $timestamps = true;
}
