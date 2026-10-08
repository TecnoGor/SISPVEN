<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstatusOficina extends Model
{
    protected $primaryKey = 'estatus_oficina_id';
    protected $table = 'estatus_oficinas';
    protected $fillable = [ 'estatus', 'create_at'];

    public function oficinas()
    {
        return $this->hasMany(Oficina::class, 'oficina_id');
    }
}
