<?php

namespace App\Http\Requests;

use App\Enums\PrioritasLaporan;
use App\Enums\UserRole;
use App\Models\Kategori;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLaporanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(UserRole::Mahasiswa) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'min:10', 'max:150'],
            'deskripsi' => ['required', 'string', 'min:20', 'max:2000'],
            'lokasi' => ['required', 'string', 'max:150'],
            'kategori_id' => [
                'required',
                'integer',
                Rule::exists(Kategori::class, 'id'),
            ],
            'fasilitas_id' => [
                'nullable',
                'integer',
                Rule::exists('fasilitas', 'id'),
            ],
            'prioritas' => [
                'required',
                Rule::enum(PrioritasLaporan::class),
            ],
            'bukti_foto' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
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
            'judul.min' => 'Judul minimal 10 karakter agar mudah dikenali.',
            'judul.max' => 'Judul maksimal 150 karakter.',
            'deskripsi.min' => 'Deskripsi minimal 20 karakter agar staff memahami masalahnya.',
            'deskripsi.max' => 'Deskripsi maksimal 2000 karakter.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak tersedia.',
            'fasilitas_id.exists' => 'Fasilitas yang dipilih tidak tersedia.',
            'prioritas.enum' => 'Prioritas yang dipilih tidak valid.',
            'bukti_foto.image' => 'Bukti foto harus berupa gambar.',
            'bukti_foto.mimes' => 'Bukti foto harus berformat JPG, JPEG, PNG, atau WEBP.',
            'bukti_foto.max' => 'Ukuran bukti foto maksimal 2 MB.',
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
            'judul' => 'judul',
            'deskripsi' => 'deskripsi',
            'lokasi' => 'lokasi',
            'kategori_id' => 'kategori',
            'fasilitas_id' => 'fasilitas',
            'prioritas' => 'prioritas',
            'bukti_foto' => 'bukti foto',
        ];
    }
}
