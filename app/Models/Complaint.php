<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Complaint extends Model
{
    use SoftDeletes;

    /**
     * Store old assigned_to values during update
     */
    protected static array $oldAssignedValues = [];

    protected $fillable = [
        'tracking_code',
        'name',
        'phone',
        'address',
        'rt',
        'rw',
        'category',
        'title',
        'description',
        'location_lat',
        'location_lng',
        'location_text',
        'status',
        'assigned_to',
        'is_anonymous',
        'images',
        'sla_deadline',
    ];

    protected $casts = [
        'images' => 'array',
        'is_anonymous' => 'boolean',
        'location_lat' => 'decimal:8',
        'location_lng' => 'decimal:8',
        'sla_deadline' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($complaint) {
            if (empty($complaint->tracking_code)) {
                $complaint->tracking_code = static::generateTrackingCode();
            }
            
            // Calculate SLA deadline based on category
            if (empty($complaint->sla_deadline)) {
                $complaint->sla_deadline = static::calculateSLADeadline($complaint->category);
            }
        });

        static::updating(function (Complaint $complaint) {
            // Store original assigned_to before update in a static property
            if ($complaint->isDirty('assigned_to')) {
                static::$oldAssignedValues[$complaint->id] = $complaint->getOriginal('assigned_to');
            }
        });

        static::updated(function (Complaint $complaint) {
            // Check if assignment was changed
            $oldAssigned = static::$oldAssignedValues[$complaint->id] ?? null;
            $newAssigned = $complaint->assigned_to;

            // Clean up
            unset(static::$oldAssignedValues[$complaint->id]);

            // Only create log if actually changed
            if ($oldAssigned != $newAssigned) {
                $assignedUser = $newAssigned ? User::find($newAssigned) : null;
                
                if ($assignedUser) {
                    $note = "Ditugaskan kepada: {$assignedUser->name}";
                } elseif ($oldAssigned) {
                    $note = "Assignment dihapus";
                } else {
                    $note = "Ditugaskan";
                }

                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'assigned',
                    'model_type' => self::class,
                    'model_id' => $complaint->id,
                    'complaint_id' => $complaint->id,
                    'note' => $note,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });
    }

    /**
     * Generate unique tracking code
     * Format: ADU-XXXXXX (6 random alphanumeric uppercase)
     */
    protected static function generateTrackingCode(): string
    {
        do {
            $code = 'ADU-' . strtoupper(Str::random(6));
        } while (static::where('tracking_code', $code)->exists());

        return $code;
    }

    /**
     * Calculate SLA deadline based on category
     * Default: 7 days for most categories
     */
    protected static function calculateSLADeadline(?string $category): \DateTime
    {
        $slaDays = match($category) {
            'urgent', 'darurat' => 1, // 1 day for urgent
            'infrastruktur', 'jalan' => 14, // 14 days for infrastructure
            'sampah', 'kebersihan' => 3, // 3 days for waste
            default => 7, // 7 days default
        };

        return now()->addDays($slaDays);
    }

    /**
     * Relationships
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(ComplaintUpdate::class)->orderBy('created_at', 'desc');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->orderBy('created_at', 'desc');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ComplaintComment::class)->orderBy('created_at', 'asc');
    }

    /**
     * Scopes
     */
    public function scopeAssignedToPetugas($query, $petugasId)
    {
        return $query->where('assigned_to', $petugasId);
    }

    public function scopeVisibleForAdmin($query)
    {
        // Admin can see all complaints
        return $query;
    }

    public function scopeVisibleForPetugas($query, $petugasId)
    {
        // Petugas can only see assigned complaints
        return $query->where('assigned_to', $petugasId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('tracking_code', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%");
        });
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOverdue($query)
    {
        return $query->where('sla_deadline', '<', now())
                    ->whereNotIn('status', ['done', 'rejected']);
    }

    public function scopeNearingDeadline($query, $days = 2)
    {
        return $query->whereBetween('sla_deadline', [now(), now()->addDays($days)])
                    ->whereNotIn('status', ['done', 'rejected']);
    }

    /**
     * Check if complaint is overdue
     */
    public function isOverdue(): bool
    {
        return $this->sla_deadline && 
               $this->sla_deadline->isPast() && 
               !in_array($this->status, ['done', 'rejected']);
    }

    /**
     * Check if complaint is nearing deadline
     */
    public function isNearingDeadline(int $days = 2): bool
    {
        return $this->sla_deadline && 
               $this->sla_deadline->isFuture() && 
               $this->sla_deadline->diffInDays(now()) <= $days &&
               !in_array($this->status, ['done', 'rejected']);
    }

    /**
     * Get SLA status color
     */
    public function getSlaStatusColor(): string
    {
        if ($this->isOverdue()) {
            return 'red';
        }
        if ($this->isNearingDeadline()) {
            return 'yellow';
        }
        return 'green';
    }

    /**
     * Check for duplicate complaints within radius (in meters)
     */
    public static function findDuplicates(float $lat, float $lng, int $radiusMeters = 100, int $daysBack = 7): \Illuminate\Database\Eloquent\Collection
    {
        // Haversine formula for distance calculation
        // Using bounding box approximation first for performance
        $latRange = $radiusMeters / 111000; // ~111km per degree latitude
        $lngRange = $radiusMeters / (111000 * cos(deg2rad($lat)));

        return static::whereBetween('location_lat', [$lat - $latRange, $lat + $latRange])
            ->whereBetween('location_lng', [$lng - $lngRange, $lng + $lngRange])
            ->where('created_at', '>=', now()->subDays($daysBack))
            ->get()
            ->filter(function ($complaint) use ($lat, $lng, $radiusMeters) {
                $distance = static::haversineDistance($lat, $lng, $complaint->location_lat, $complaint->location_lng);
                return $distance <= $radiusMeters;
            });
    }

    /**
     * Calculate distance between two points using Haversine formula
     * Returns distance in meters
     */
    protected static function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // Earth radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get privacy-filtered data for petugas
     */
    public function getPublicData(): array
    {
        return [
            'id' => $this->id,
            'tracking_code' => $this->tracking_code,
            'category' => $this->category,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'location_text' => $this->location_text,
            'rt' => $this->rt,
            'rw' => $this->rw,
            'created_at' => $this->created_at,
            'sla_deadline' => $this->sla_deadline,
        ];
    }
}
