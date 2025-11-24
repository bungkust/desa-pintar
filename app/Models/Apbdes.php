<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apbdes extends Model
{
    protected $guarded = [];

    protected $casts = [
        'year' => 'integer',
        'amount' => 'integer',
        'realisasi' => 'integer',
        'anggaran' => 'integer',
    ];
}

