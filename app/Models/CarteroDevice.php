<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarteroDevice extends Model
{
    use HasFactory;

    protected $table = 'cartero_devices';

    protected $fillable = [
        'user_id',
        'fcm_token',
        'platform',
        'app_version',
        'last_seen',
    ];

    protected $casts = [
        'last_seen' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
