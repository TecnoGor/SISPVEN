<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SacaEstatus extends Model
{
    protected $table = 'sacas_estatus';
    protected $primaryKey = 'saca_estatus_id';

    protected $fillable = ['nombre'];

    use HasFactory;
}
