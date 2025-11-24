<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Role helper methods
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdminDesa(): bool
    {
        return $this->role === 'admin_desa';
    }

    public function isLurah(): bool
    {
        return $this->role === 'lurah';
    }

    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    public function canManageComplaints(): bool
    {
        return in_array($this->role, ['super_admin', 'admin_desa', 'lurah']);
    }

    public function canAssignPetugas(): bool
    {
        return in_array($this->role, ['super_admin', 'admin_desa']);
    }

    public function canViewPrivateData(): bool
    {
        return in_array($this->role, ['super_admin', 'admin_desa', 'lurah']);
    }

    /**
     * Relationships
     */
    public function assignedComplaints()
    {
        return $this->hasMany(Complaint::class, 'assigned_to');
    }

    public function complaintUpdates()
    {
        return $this->hasMany(ComplaintUpdate::class, 'updated_by');
    }

    public function complaintComments()
    {
        return $this->hasMany(ComplaintComment::class, 'user_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Determine if the user can access the Filament admin panel.
     * In production, only allow access if explicitly configured.
     * In local environment, allow all authenticated users.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Allow access in local environment for development
        if (config('app.env') === 'local') {
            return true;
        }

        // In production, require email verification and optionally restrict by domain
        // TODO: Configure allowed domains via .env (e.g., APP_ADMIN_EMAIL_DOMAIN)
        $allowedDomain = config('app.admin_email_domain');
        
        if ($allowedDomain && !str_ends_with($this->email, '@' . $allowedDomain)) {
            return false;
        }

        // Require email verification in production
        return $this->hasVerifiedEmail();
    }
}
