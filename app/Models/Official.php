<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Official extends Model
{
    protected $fillable = [
        'name',
        'position',
        'photo',
        'greeting',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];
}

