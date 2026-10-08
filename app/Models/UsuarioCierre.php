<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioCierre extends Model
{
    use HasFactory;

    protected $table = 'usuario_cierre';
    protected $primaryKey = 'usuario_cierre_id';

    protected $fillable = [
        'usuario_id',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

}
