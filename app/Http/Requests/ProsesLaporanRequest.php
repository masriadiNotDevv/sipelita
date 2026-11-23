<?php

namespace App\Http\Requests;

use App\Enums\StatusLaporan;
use App\Models\Laporan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProsesLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $laporan = $this->route('laporan');

        return $laporan instanceof Laporan
            && $this->user()?->can('proses', $laporan) === true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(StatusLaporan::class)],
            'isi' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status laporan wajib dipilih.',
            'status.enum' => 'Status laporan tidak valid.',
            'isi.required' => 'Tanggapan wajib diisi.',
            'isi.min' => 'Tanggapan minimal 10 karakter.',
            'isi.max' => 'Tanggapan maksimal 1000 karakter.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'status' => 'status',
            'isi' => 'tanggapan',
        ];
    }
}
