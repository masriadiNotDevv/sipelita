<?php

namespace App\Http\Requests;

use App\Enums\PrioritasLaporan;
use App\Models\Kategori;
use App\Models\Laporan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLaporanAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        $laporan = $this->route('laporan');

        return $laporan instanceof Laporan
            && $this->user()?->can('update', $laporan) === true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'min:10', 'max:150'],
            'deskripsi' => ['required', 'string', 'min:20', 'max:2000'],
            'lokasi' => ['required', 'string', 'max:150'],
            'kategori_id' => ['required', 'integer', Rule::exists(Kategori::class, 'id')],
            'fasilitas_id' => ['nullable', 'integer', Rule::exists('fasilitas', 'id')],
            'prioritas' => ['required', Rule::enum(PrioritasLaporan::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.min' => 'Judul minimal 10 karakter.',
            'judul.max' => 'Judul maksimal 150 karakter.',
            'deskripsi.min' => 'Deskripsi minimal 20 karakter.',
            'deskripsi.max' => 'Deskripsi maksimal 2000 karakter.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak tersedia.',
            'fasilitas_id.exists' => 'Fasilitas yang dipilih tidak tersedia.',
            'prioritas.enum' => 'Prioritas yang dipilih tidak valid.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'judul' => 'judul',
            'deskripsi' => 'deskripsi',
            'lokasi' => 'lokasi',
            'kategori_id' => 'kategori',
            'fasilitas_id' => 'fasilitas',
            'prioritas' => 'prioritas',
        ];
    }
}
