<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $guarded = [];

    protected $casts = [
        'order' => 'integer',
    ];

    public function details()
    {
        return $this->hasMany(StatisticDetail::class)->orderBy('year', 'desc');
    }

    public function getValueByYear($year)
    {
        return $this->details()->where('year', $year)->first();
    }
}

