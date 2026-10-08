<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoImprenta extends Model
{
    protected $table = 'pedidos_imprentas';
    protected $primaryKey = 'pedido_imprenta_id';
    protected $fillable = ['nombre', 'apellido', 'tipo_documento', 'documento', 'correo', 'telefono', 'oficina_id', 'usuario_id', 
    'alto', 'ancho', 'fecha_entrega', 'imagen_path', 'nombre_original', 'activo', 'create_at'];

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'oficina_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }

    use HasFactory;
}
