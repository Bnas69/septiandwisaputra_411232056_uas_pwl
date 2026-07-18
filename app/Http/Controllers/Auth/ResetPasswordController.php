<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:64',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,64}$/',
            ],
        ]);

        try {
            $resetUser = null;

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) use (&$resetUser) {
                    $resetUser = $user;

                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($user));
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                AuditLog::log('password_reset', 'Password berhasil direset untuk: ' . $request->email);

                if ($resetUser) {
                    DB::table(config('session.table'))
                        ->where('user_id', $resetUser->id)
                        ->delete();
                }

                return redirect()->route('login')->with('status', __($status));
            }

            return back()->withErrors(['email' => __($status)]);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal mereset password. Silakan coba lagi.');
        }
    }
}
