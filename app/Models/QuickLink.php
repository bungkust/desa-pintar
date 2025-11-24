<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickLink extends Model
{
    protected $fillable = [
        'label',
        'icon_class',
        'url',
        'color',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];
}

