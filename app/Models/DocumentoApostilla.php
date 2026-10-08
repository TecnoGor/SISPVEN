<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoApostilla extends Model
{
    protected $table= 'documentos_apostillas';
    protected $primaryKey= 'documento_apostilla_id';
    protected $fillable= ['documento'];
    
    public function documento_apostilla()
    {
        return $this->belongsTo(DocumentoApostilla::class, 'documento_apostilla_id', 'documento_apostilla_id');
    }


    use HasFactory;
}
