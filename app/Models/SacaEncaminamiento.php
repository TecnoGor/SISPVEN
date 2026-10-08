<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SacaEncaminamiento extends Model
{
    use HasFactory;

    protected $table = 'sacas_encaminamiento';
    protected $primaryKey = 'saca_encaminamiento_id';

    protected $fillable = [
        'saca_id',
        'oficina_id',
        'oficina_externa_id',
        'saca_estatus_id',
        'usuario_id',
    ];

    public function saca()
    {
        return $this->belongsTo(Saca::class, 'saca_id', 'saca_id');
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    public function oficinaExterna()
    {
        return $this->belongsTo(Oficina::class, 'oficina_externa_id', 'oficina_id');
    }

    public function sacaEstatus()
    {
        return $this->belongsTo(SacaEstatus::class, 'saca_estatus_id', 'saca_estatus_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }
}
