<?php

namespace App\Http\Requests\Lhp;

use Illuminate\Foundation\Http\FormRequest;

class StoreLhpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // =====================
            // DATA LHP
            // =====================
            'pkpt_id' => ['required', 'exists:pkpts,id'],
            'auditi_id' => ['required', 'exists:auditis,id'],
            'nomor_lhp' => ['nullable', 'string', 'max:255'],
            'tanggal_lhp' => ['required', 'date', 'after_or_equal:today'],
            'uraian_temuan' => ['nullable', 'string'],
            'rekomendasi' => ['nullable', 'string'],
            'file_lhp' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],

            // =====================
            // TEMUAN (ARRAY)
            // =====================
            'temuans' => ['required', 'array', 'min:1'],
            'temuans.*.judul_temuan' => ['required', 'string', 'max:255'],
            'temuans.*.kode_temuan_id' => ['required', 'exists:kode_temuans,id'],
            'temuans.*.kondisi_temuan' => ['required', 'string'],
            'temuans.*.kriteria_temuan' => ['nullable', 'string'],
            'temuans.*.sebab_temuan' => ['nullable', 'string'],
            'temuans.*.akibat_temuan' => ['nullable', 'string'],

            // =====================
            // REKOMENDASI DALAM TEMUAN
            // =====================
            'temuans.*.rekomendasis' => ['required', 'array', 'min:1'],
            'temuans.*.rekomendasis.*.kode_rekomendasi_id' => ['required', 'exists:kode_rekomendasis,id'],
            'temuans.*.rekomendasis.*.rekomendasi_temuan' => ['required', 'string'],
            'temuans.*.rekomendasis.*.nominal' => ['nullable', 'numeric', 'min:0'],

            // =====================
            // FILE PENDUKUNG (PER TEMUAN)
            // =====================
            'temuans.*.files' => ['required', 'array'], // nested di setiap temuan
            'temuans.*.files.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            // Data LHP
            'pkpt_id.required' => 'PKPT wajib dipilih.',
            'tanggal_lhp.required' => 'Tanggal LHP wajib diisi.',
            'tanggal_lhp.after_or_equal' => 'Tanggal LHP tidak boleh sebelum hari ini.',
            'file_lhp.mimes' => 'File LHP hanya boleh bertipe PDF, DOC, atau DOCX.',

            // Temuan
            'temuans.required' => 'Minimal satu temuan harus diisi.',
            'temuans.*.judul_temuan.required' => 'Judul temuan wajib diisi.',
            'temuans.*.kode_temuan_id.required' => 'Kode temuan wajib dipilih.',
            'temuans.*.kondisi_temuan.required' => 'Kondisi temuan wajib diisi.',

            // Rekomendasi
            'temuans.*.rekomendasis.required' => 'Setiap temuan minimal memiliki satu rekomendasi.',
            'temuans.*.rekomendasis.*.kode_rekomendasi_id.required' => 'Kode rekomendasi wajib dipilih.',
            'temuans.*.rekomendasis.*.rekomendasi_temuan.required' => 'Uraian rekomendasi wajib diisi.',

            // File pendukung
            'temuans.*.files.required' => 'Minimal satu file pendukung harus ada di setiap temuan.',
            'temuans.*.files.*.mimes' => 'File pendukung harus berupa PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, atau PNG.',
        ];
    }

    /**
     * Validasi tambahan: pastikan minimal 1 file di semua temuan
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $temuans = $this->file('temuans', []);
            $totalFiles = 0;

            if ($temuans && is_array($temuans)) {
                foreach ($temuans as $temuan) {
                    if (!empty($temuan['files'])) {
                        $totalFiles += count($temuan['files']);
                    }
                }
            }

            if ($totalFiles < 1) {
                $validator->errors()->add('temuans', 'Minimal satu file pendukung harus ada di semua temuan.');
            }
        });
    }
}
