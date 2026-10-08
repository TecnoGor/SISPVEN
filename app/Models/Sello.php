<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sello extends Model
{
    protected $table = 'sellos';
    protected $primaryKey = 'sello_id';
    protected $fillable = ['nombre', 'serie_filatelia_id', 'coste', 'activo'];


    public function serie()
    {    
        return $this->belongsTo(SerieFilatelia::class, 'serie_filatelia_id', 'serie_filatelia_id');
    }

    use HasFactory;
}
