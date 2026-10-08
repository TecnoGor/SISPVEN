<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apostillas extends Model
{
    protected $table= 'apostillas';
    protected $primaryKey= 'apostilla_id';
    protected $fillable= ['documento_apostilla_id', 'cita_apostilla_id'];
    
    public function documento_apostilla()
    {
        return $this->belongsTo(DocumentoApostilla::class, 'documento_apostilla_id', 'documento_apostilla_id');
    }

    public function cita_apostilla()
    {
        return $this->belongsTo(CitaApostilla::class, 'cita_apostilla_id', 'cita_apostilla_id');
    }

    use HasFactory;
}
