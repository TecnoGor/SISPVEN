<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaIposplus extends Model
{
    protected $table = "tarifas_iposplus";
    protected $primaryKey = "tarifa_iposplus_id";
    protected $fillable = ['kilo', 'precio', 'created_at', 'updated_at'];
    use HasFactory;
}
