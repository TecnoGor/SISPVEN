<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GastoArrendamiento extends Model
{
    protected $table = 'gastos_arrendamiento';
    protected $primaryKey = 'gasto_arrendamiento_id';
    protected $fillable = ['oficina_id', 'usuario_id', 'fecha', 'monto', 'estatus'];

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id', 'oficina_id');
    }

    use HasFactory;
}
