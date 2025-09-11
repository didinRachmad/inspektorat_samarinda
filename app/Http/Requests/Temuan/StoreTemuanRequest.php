<?php

namespace App\Http\Requests\Temuan;

use Illuminate\Foundation\Http\FormRequest;

class StoreTemuanRequest extends FormRequest
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
    public function rules()
    {
        return [
            'lha_id' => [
                'required',
                'exists:lhas,id',
            ],

            // Data utama temuan
            'kode_temuan_id' => [
                'required',
                'exists:kode_temuans,id',
            ],
            'judul_temuan'    => 'required|string|max:255',
            'kondisi_temuan'  => 'required|string',
            'kriteria_temuan' => 'nullable|string',
            'sebab_temuan'    => 'nullable|string',
            'akibat_temuan'   => 'nullable|string',

            // Rekomendasi (dinamis, bisa banyak)
            'rekomendasis'                       => 'nullable|array',
            'rekomendasis.*.kode_rekomendasi_id' => 'nullable|exists:kode_rekomendasis,id',
            'rekomendasis.*.rekomendasi_temuan'  => 'nullable|string|max:500',

            // File upload (dinamis, multiple)
            'files'   => 'nullable|array',
            'files.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10 MB per file
        ];
    }
}
