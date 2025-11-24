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
