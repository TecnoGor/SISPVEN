<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoRemitenteTelegrama extends Model
{
    // Nombre exacto de la tabla (migración creó 'tipos_remitente_telegramas')
    protected $table = 'tipos_remitente_telegramas';

    // PK con el nombre que usaste en la migración
    protected $primaryKey = 'tipos_remitente_telegramas_id';

    public $timestamps = true;

    protected $fillable = ['nombre'];
}
