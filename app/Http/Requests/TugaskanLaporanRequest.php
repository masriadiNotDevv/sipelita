<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TugaskanLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $laporan = $this->route('laporan');

        return $laporan instanceof Laporan
            && $this->user()?->can('tugaskan', $laporan) === true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'penangani_id' => [
                'nullable',
                'integer',
                Rule::exists(User::class, 'id')->where('role', UserRole::Staff->value),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'penangani_id.exists' => 'Penangani harus berupa akun staff yang tersedia.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'penangani_id' => 'penangani',
        ];
    }
}
