<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipientRequirementConfig extends Model
{
    use HasFactory;

    protected $table = 'recipient_requirement_config';
    protected $primaryKey = 'field_key';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'field_key',
        'label',
        'is_required',
        'updated_at',
        'updated_by',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'updated_at' => 'datetime',
    ];
}
