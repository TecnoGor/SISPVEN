<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivoNpcOficina extends Model
{
    use HasFactory;

    protected $table = 'motivo_npc_oficina'; 

    protected $primaryKey = 'motivo_npc_oficina_id';

    protected $fillable = [
        'oficina_id',
        'motivo',
        'file_path',
        'nombre_original',
        'created_at',
        'updated_at'
    ];

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }
}
