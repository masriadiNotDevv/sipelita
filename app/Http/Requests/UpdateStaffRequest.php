<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateStaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(UserRole::Admin) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->staff()?->id),
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => [
                'required',
                Rule::enum(UserRole::class)->only([UserRole::Admin, UserRole::Staff]),
            ],
        ];
    }

    /**
     * Akun yang sedang diedit, sudah dibatasi oleh route binding `staff`.
     */
    private function staff(): ?User
    {
        $staff = $this->route('staff');

        return $staff instanceof User ? $staff : null;
    }

    /**
     * Validasi tambahan yang bergantung pada lebih dari satu field.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $staff = $this->staff();

                if ($staff === null || ! $this->user()?->is($staff)) {
                    return;
                }

                if ($this->input('role') !== $staff->role->value) {
                    $validator->errors()->add('role', 'Anda tidak dapat mengubah peran akun Anda sendiri.');
                }
            },
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role.required' => 'Peran wajib dipilih.',
            'role.enum' => 'Peran hanya dapat berupa admin atau staff.',
            'email.unique' => 'Email tersebut sudah digunakan akun lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'email' => 'email',
            'password' => 'password',
            'role' => 'peran',
        ];
    }
}
