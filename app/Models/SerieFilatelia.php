<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerieFilatelia extends Model
{
    protected $table = 'series_filatelias';
    protected $primaryKey = 'serie_filatelia_id';
    protected $fillable = ['nombre', 'activo'];

    public function sellos()
    {
        return $this->hasMany(Sello::class, 'serie_filatelia_id', 'serie_filatelia_id');
    }


    use HasFactory;
}
