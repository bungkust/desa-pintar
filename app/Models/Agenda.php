<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'date',
        'start_time',
        'end_time',
        'location',
        'organizer',
        'contact_person',
        'google_maps_url',
        'image',
        'is_featured',
        'is_recurring',
        'recurring_type',
    ];

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

