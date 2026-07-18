<?php

namespace App\Services\Auth;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class LoginService
{
    public function attemptLogin(Request $request): array
    {
        $throttleKey = 'login:' . strtolower($request->email);

        if (RateLimiter::tooManyAttempts($throttleKey, User::MAX_LOGIN_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return [
                'success' => false,
                'message' => "Terlalu banyak percobaan gagal. Coba lagi dalam {$seconds} detik.",
            ];
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, User::LOCKOUT_MINUTES);

            if ($user) {
                $user->recordFailedLogin();
                AuditLog::log('login_failed', 'Percobaan login gagal untuk: ' . $request->email);
            }

            $remaining = $user ? $user->getRemainingAttempts() : User::MAX_LOGIN_ATTEMPTS;

            return [
                'success' => false,
                'message' => "Email atau password salah. Sisa percobaan: {$remaining}",
            ];
        }

        if (! $user->isActive()) {
            Auth::logout();
            $status = $user->status === 'suspended' ? 'ditangguhkan' : 'tidak aktif';

            return [
                'success' => false,
                'message' => "Akun Anda {$status}. Hubungi administrator.",
            ];
        }

        if ($user->isLocked()) {
            Auth::logout();

            return [
                'success' => false,
                'message' => 'Akun Anda terkunci selama ' . $user->getLockMinutesRemaining() . ' menit.',
            ];
        }

        RateLimiter::clear($throttleKey);
        $user->recordLogin($request->ip());
        $request->session()->regenerate();

        AuditLog::log('login', 'Berhasil login');

        return [
            'success' => true,
            'message' => 'Login berhasil.',
        ];
    }
}
