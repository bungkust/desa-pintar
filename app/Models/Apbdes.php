<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apbdes extends Model
{
    protected $fillable = [
        'year',
        'type',
        'category',
        'amount',
        'realisasi',
        'anggaran',
    ];

    protected $casts = [
        'year' => 'integer',
        'amount' => 'integer',
        'realisasi' => 'integer',
        'anggaran' => 'integer',
    ];
}

