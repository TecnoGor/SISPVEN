<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LugarEmisionTelegrama extends Model
{
    protected $table = 'lugar_emision_telegramas';
    protected $primaryKey = 'lugar_emision_telegramas_id';

    public $timestamps = true;
    protected $fillable = ['nombre'];

    public function circuitos()
    {
        return $this->hasMany(CircuitoJudicialTribunalTelegrama::class, 'lugar_emision_telegramas_id', 'lugar_emision_telegramas_id');
    }
}
