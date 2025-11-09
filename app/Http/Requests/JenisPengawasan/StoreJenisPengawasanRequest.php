<?php

namespace App\Http\Requests\JenisPengawasan;

use Illuminate\Foundation\Http\FormRequest;

class StoreJenisPengawasanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ubah jika mau cek permission
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|unique:jenis_pengawasans,nama',
            'parent_id' => 'nullable|exists:jenis_pengawasans,id',
            'urutan' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama Jenis Pengawasan wajib diisi.',
            'nama.unique' => 'Nama Jenis Pengawasan sudah ada.',
            'parent_id.exists' => 'Parent tidak valid.'
        ];
    }
}