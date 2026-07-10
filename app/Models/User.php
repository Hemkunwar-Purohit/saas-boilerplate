<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\CausesActivity;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, CausesActivity, HasApiTokens;

    protected $fillable = [
        'name', 'email', 'password', 'avatar',
        'phone', 'timezone', 'locale', 'is_active', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'is_active'         => 'boolean',
        'password'          => 'hashed',
    ];

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && tenant()) {
            // Tenant domain + avatar route
            $host   = request()->getHost();
            $scheme = request()->isSecure() ? 'https' : 'http';
            $port   = request()->getPort() != 80 && request()->getPort() != 443
                ? ':' . request()->getPort() : '';

            return "{$scheme}://{$host}{$port}/avatar/{$this->avatar}";
        }

        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=6366f1&color=fff&size=128";
    }

    public function isOwner(): bool { return $this->hasRole('owner'); }
    public function isAdmin(): bool { return $this->hasRole(['owner', 'admin']); }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}
