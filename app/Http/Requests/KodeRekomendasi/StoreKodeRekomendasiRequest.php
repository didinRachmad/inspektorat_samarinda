<?php

namespace App\Http\Requests\KodeRekomendasi;

use Illuminate\Foundation\Http\FormRequest;

class StoreKodeRekomendasiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'max:10', 'unique:kode_rekomendasis,kode'],
            'nama_rekomendasi' => ['required', 'string', 'max:255'],
            'urutan' => ['nullable', 'integer', 'min:0'],

            // Validasi pivot temuan_ids
            'temuan_ids' => ['nullable', 'array'],
            'temuan_ids.*' => ['exists:kode_temuans,id'],
        ];
    }

    /**
     * Custom messages for validation
     */
    public function messages(): array
    {
        return [
            'kode.required' => 'Kode rekomendasi wajib diisi.',
            'kode.unique' => 'Kode rekomendasi sudah digunakan.',
            'nama_rekomendasi.required' => 'Nama rekomendasi wajib diisi.',
            'urutan.integer' => 'Urutan harus berupa angka.',
            'temuan_ids.array' => 'Temuan terkait harus berupa array.',
            'temuan_ids.*.exists' => 'Temuan terkait tidak valid.',
        ];
    }
}
