<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\Auth\LoginService;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request, LoginService $loginService)
    {
        try {
            $result = $loginService->attemptLogin($request);

            if (!is_array($result) || empty($result['success'])) {
                return back()
                    ->withInput($request->only('email', 'remember'))
                    ->with('error', $result['message'] ?? 'Login gagal. Silakan coba lagi.');
            }

            return $this->redirectToDashboard();
        } catch (\Throwable $e) {
            report($e);
            return back()
                ->withInput($request->only('email', 'remember'))
                ->with('error', 'Terjadi kesalahan saat login. Silakan coba lagi.');
        }
    }

    private function redirectToDashboard()
    {
        return redirect()->route('dashboard');
    }
}
