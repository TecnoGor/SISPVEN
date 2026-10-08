<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    protected $table = 'direcciones';
    protected $primaryKey = 'direccion_id';
    protected $fillable = [
        'empleado_id',
        'estado_id',
        'municipio_id',
        'parroquia_id',
        'sector_id',
        'direccion_especifica',
        'principal',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }
    use HasFactory;
}
