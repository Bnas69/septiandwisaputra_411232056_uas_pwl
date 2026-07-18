<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    public const MAX_LOGIN_ATTEMPTS = 5;
    public const LOCKOUT_MINUTES = 15;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role', 'avatar', 'status',
        'last_login_at', 'last_login_ip', 'failed_login_attempts', 'locked_until',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function isDeveloper(): bool
    {
        return $this->role === 'developer';
    }

    public function recordLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    public function recordFailedLogin(): void
    {
        $attempts = $this->failed_login_attempts + 1;
        $lockedUntil = null;

        if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
            $cycle = (int) floor($attempts / self::MAX_LOGIN_ATTEMPTS);
            $minutes = self::LOCKOUT_MINUTES * max(1, $cycle);
            $lockedUntil = now()->addMinutes($minutes);
        }

        $this->update([
            'failed_login_attempts' => $attempts,
            'locked_until' => $lockedUntil,
        ]);
    }

    public function getRemainingAttempts(): int
    {
        return max(0, self::MAX_LOGIN_ATTEMPTS - $this->failed_login_attempts);
    }

    public function getLockMinutesRemaining(): int
    {
        if (! $this->locked_until || ! $this->locked_until->isFuture()) {
            return 0;
        }
        return (int) ceil($this->locked_until->diffInMinutes(now()));
    }
}
