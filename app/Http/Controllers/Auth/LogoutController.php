<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        $userId = Auth::id();

        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            AuditLog::log('logout', 'User logout', null, [
                'user_id' => $userId,
            ]);
        } catch (\Throwable $e) {
            // Audit logging failure should not block logout
        }

        return redirect()->route('login');
    }
}
