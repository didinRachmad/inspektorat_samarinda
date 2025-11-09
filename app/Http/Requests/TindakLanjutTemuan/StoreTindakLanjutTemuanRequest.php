<?php

namespace App\Http\Requests\TindakLanjutTemuan;

use Illuminate\Foundation\Http\FormRequest;

class StoreTindakLanjutTemuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pkpt_id' => ['required', 'exists:pkpts,id'],
            'auditi_id' => ['required', 'exists:auditis,id'],
            'nomor_lhp' => ['nullable', 'string', 'max:255'],
            'tanggal_lhp' => ['required', 'date', 'after_or_equal:today'],
            'uraian_temuan' => ['nullable', 'string'],
            'rekomendasi' => ['nullable', 'string'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'pkpt_id.required' => 'PKPT wajib dipilih.',
            'tanggal_lhp.required' => 'Tanggal LHP wajib diisi.',
            'tanggal_lhp.after_or_equal' => 'Tanggal LHP tidak boleh sebelum hari ini.',
            'lampiran.mimes' => 'File LHP hanya boleh bertipe PDF, DOC, atau DOCX.',
        ];
    }
}
