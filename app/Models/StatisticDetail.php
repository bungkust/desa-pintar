<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticDetail extends Model
{
    protected $fillable = [
        'statistic_id',
        'year',
        'value',
        'additional_data',
    ];

    protected $casts = [
        'year' => 'integer',
        'additional_data' => 'array',
    ];

    public function statistic()
    {
        return $this->belongsTo(Statistic::class);
    }
}
