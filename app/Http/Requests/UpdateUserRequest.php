<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'username' => strtolower(trim($this->username ?? '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[a-zA-Z\s]+$/',
            ],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-z0-9_]+$/',
                'unique:users,username,' . $this->route('user'),
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'unique:users,email,' . $this->route('user'),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'max:64',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,64}$/',
            ],
            'role' => [
                'required',
                'string',
                'in:developer,owner,pegawai,user',
            ],
            'status' => [
                'required',
                'string',
                'in:active,inactive,suspended',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.min' => 'Nama minimal 3 karakter.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',

            'username.required' => 'Username wajib diisi.',
            'username.string' => 'Username harus berupa teks.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username maksimal 50 karakter.',
            'username.regex' => 'Username hanya boleh huruf kecil, angka, dan garis bawah.',
            'username.unique' => 'Username sudah digunakan oleh pengguna lain.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',

            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.max' => 'Password maksimal 64 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol (@$!%*#?&).',

            'role.required' => 'Role wajib dipilih.',
            'role.string' => 'Role harus berupa teks.',
            'role.in' => 'Role hanya boleh: developer, owner, pegawai, atau user.',

            'status.required' => 'Status wajib dipilih.',
            'status.string' => 'Status harus berupa teks.',
            'status.in' => 'Status hanya boleh: active, inactive, atau suspended.',
        ];
    }
}
