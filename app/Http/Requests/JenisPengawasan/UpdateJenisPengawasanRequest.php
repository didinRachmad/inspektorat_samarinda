<?php

namespace App\Http\Requests\JenisPengawasan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJenisPengawasanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ubah jika mau cek permission
    }

    public function rules(): array
    {
        $id = $this->route('jenis_pengawasan')->id;

        return [
            'nama' => [
                'required',
                'string',
                Rule::unique('jenis_pengawasans', 'nama')->ignore($id)
            ],
            'parent_id' => [
                'nullable',
                'exists:jenis_pengawasans,id',
                // tidak boleh jadi parent dirinya sendiri
                function ($attribute, $value, $fail) use ($id) {
                    if ($value == $id) {
                        $fail('Parent tidak boleh sama dengan Jenis Pengawasan itu sendiri.');
                    }
                }
            ],
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