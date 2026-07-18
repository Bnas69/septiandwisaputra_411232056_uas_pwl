<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                AuditLog::log('password_reset_requested', 'Link reset password dikirim ke: ' . $request->email);
                return back()->with('status', __($status));
            }

            return back()->withErrors(['email' => __($status)]);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal mengirim link reset. Silakan coba lagi.');
        }
    }
}
