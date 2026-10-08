<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsumoUsuarioTransferencia extends Model
{
    protected $table = 'insumos_usuario_transferencia';
    protected $primaryKey = 'insumo_usuario_transferencia_id';
    protected $fillable = ['usuario_id', 'insumo_id', 'cantidad', 'coste'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id', 'insumo_id');
    }


    use HasFactory;
}
