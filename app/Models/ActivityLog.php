<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'complaint_id',
        'status_from',
        'status_to',
        'meta',
        'note',
        'image',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scopes
     */
    public function scopeForComplaint($query, $complaintId)
    {
        return $query->where('complaint_id', $complaintId);
    }

    public function scopeForModel($query, $modelType, $modelId = null)
    {
        $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeStatusChanges($query)
    {
        return $query->where('action', 'status_changed');
    }

    /**
     * Helper methods
     */
    public function isStatusChange(): bool
    {
        return $this->action === 'status_changed' && $this->complaint_id !== null;
    }

    public function isComplaintActivity(): bool
    {
        return $this->complaint_id !== null;
    }
}
