<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'user_id',
        'otp',
        'expires_at',
        'has_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'has_used' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
