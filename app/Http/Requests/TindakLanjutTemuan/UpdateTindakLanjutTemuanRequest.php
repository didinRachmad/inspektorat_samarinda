<?php

namespace App\Http\Requests\TindakLanjutTemuan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTindakLanjutTemuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization sudah dicek di controller
        return true;
    }

    public function rules(): array
    {
        return [
            'deskripsi_tindak_lanjut' => ['required', 'string'],
            'tanggal_tindak_lanjut'   => ['nullable', 'date'],
            'lampiran.*'              => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'], // maksimal 10MB per file
        ];
    }

    public function messages(): array
    {
        return [
            'deskripsi_tindak_lanjut.required' => 'Deskripsi tindak lanjut wajib diisi.',
            'deskripsi_tindak_lanjut.string'   => 'Deskripsi tindak lanjut harus berupa teks.',
            'tanggal_tindak_lanjut.date'       => 'Tanggal tindak lanjut tidak valid.',
            'lampiran.*.mimes'                 => 'File lampiran hanya boleh berupa PDF, DOC, atau DOCX.',
            'lampiran.*.max'                   => 'Ukuran file lampiran maksimal 10 MB per file.',
        ];
    }
}
