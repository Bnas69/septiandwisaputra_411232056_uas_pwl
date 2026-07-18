<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(protected UserService $userService)
    {
    }

    public function index()
    {
        try {
            $users = $this->userService->list();

            return view('users.index', compact('users'));
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->back()
                ->with('error', 'Gagal memuat data user.');
        }
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->userService->create($request->validated());

            AuditLog::log(
                'user_created',
                'User baru dibuat: ' . $user->username,
                $user
            );

            return redirect()
                ->route('settings.users.index')
                ->with('success', 'User berhasil ditambahkan.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal membuat user.');
        }
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $oldRole = $user->role;

            $this->userService->update($user, $request->validated());

            $user->refresh();

            if ($oldRole !== $user->role) {
                AuditLog::log(
                    'role_changed',
                    'Role diubah',
                    $user,
                    [
                        'old_values' => [
                            'role' => $oldRole,
                        ],
                        'new_values' => [
                            'role' => $user->role,
                        ],
                    ]
                );
            }

            return redirect()
                ->route('settings.users.index')
                ->with('success', 'User berhasil diperbarui.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui user.');
        }
    }

    public function destroy(User $user)
    {
        if ($user->id == Auth::id()) {
            return redirect()
                ->back()
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        if (method_exists($user, 'isDeveloper') && $user->isDeveloper()) {
            return redirect()
                ->back()
                ->with('error', 'Akun developer tidak dapat dihapus.');
        }

        try {
            $this->userService->delete($user);

            AuditLog::log(
                'user_deleted',
                'User dihapus: ' . $user->username,
                $user
            );

            return redirect()
                ->route('settings.users.index')
                ->with('success', 'User berhasil dihapus.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus user.');
        }
    }

    public function profile()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        return view('users.profile', compact('user'));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        try {

            $oldData = [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
            ];

            $validated = $request->validated();

            $this->userService->updateProfile($user, $validated);

            $user->refresh();

            AuditLog::log(
                'profile_updated',
                'Profil diperbarui',
                $user,
                [
                    'old_values' => $oldData,
                    'new_values' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                    ],
                ]
            );

            return redirect()
                ->back()
                ->with('success', 'Profil berhasil diperbarui.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui profil.');
        }
    }
}