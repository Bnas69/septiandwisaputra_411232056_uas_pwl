<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function showForm()
    {
        return view('auth.change-password');
    }

    public function change(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        try {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
            }

            $user->update(['password' => $request->password]);

            $sessionTable = config('session.table');

            if ($sessionTable) {
                DB::table($sessionTable)
                    ->where('user_id', $user->id)
                    ->where('id', '!=', $request->session()->getId())
                    ->delete();
            }

            AuditLog::log('password_changed', 'Password berhasil diubah');

            return back()->with('success', 'Password berhasil diubah. Semua sesi lain telah ditutup.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengubah password. Silakan coba lagi.');
        }
    }
}
