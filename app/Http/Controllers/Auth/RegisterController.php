<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\AuditLog;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->validated();
            $data['role'] = 'user';

            $user = $this->userService->create($data);

            AuditLog::log('register', 'Akun baru dibuat: ' . $user->username, $user);

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        } catch (\Throwable $e) {
            report($e);
            return back()
                ->withInput($request->only('name', 'username', 'email'))
                ->with('error', 'Gagal membuat akun. Silakan coba lagi.');
        }
    }
}
