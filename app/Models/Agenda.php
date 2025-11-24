<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_featured' => 'boolean',
        'is_recurring' => 'boolean',
    ];

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc');
    }

    public function getCategoryLabelAttribute()
    {
        $labels = [
            'pemerintahan' => 'Pemerintahan',
            'kesehatan' => 'Kesehatan',
            'lingkungan' => 'Lingkungan',
            'budaya' => 'Budaya',
            'umum' => 'Umum',
        ];

        return $labels[$this->category] ?? $this->category;
    }
}

