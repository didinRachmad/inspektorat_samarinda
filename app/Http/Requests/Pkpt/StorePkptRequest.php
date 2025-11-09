<?php

namespace App\Http\Requests\Pkpt;

use Illuminate\Foundation\Http\FormRequest;

class StorePkptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // bisa pakai Gate/Policy jika perlu
    }

    public function rules(): array
    {
        return [
            // Periode
            'tahun' => 'required|digits:4',
            'bulan' => 'nullable|integer|min:1|max:12',
            'no_pkpt' => 'nullable|string|max:255',

            // Relasi utama
            'mandatory_id' => 'required|exists:mandatories,id',
            'jenis_pengawasan_id' => 'required|exists:jenis_pengawasans,id',
            'irbanwil_id' => 'nullable|exists:irbanwils,id',

            // Multiple auditi
            'auditis' => 'required|array|min:1',
            'auditis.*' => 'exists:auditis,id',

            // Data PKPT
            'ruang_lingkup' => 'nullable|string|max:255',
            'sasaran' => 'required|string|max:255',
            'jadwal_rmp_bulan' => 'nullable|integer|between:1,12',
            'jadwal_rsp_bulan' => 'nullable|integer|between:1,12',
            'jadwal_rpl_bulan' => 'nullable|integer|between:1,12',
            'jadwal_hp_hari' => 'nullable|integer|min:1',
            'auditor_ringkas' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:500',
            'pkpt' => 'nullable|integer|min:0',

            // Nested jabatans
            'jabatans' => 'required|array|min:1',
            'jabatans.*.jabatan' => 'required|in:PJ,WPJ,PT,KT,AT',
            'jabatans.*.jumlah' => 'required|integer|min:1',
            'jabatans.*.anggaran' => 'required|integer|min:0',

            // Nested auditors
            'auditors' => 'nullable|array',
            'auditors.*.user_id' => 'nullable|exists:users,id',
            'auditors.*.nama_manual' => 'nullable|string|max:255',
            'auditors.*.jabatan' => 'nullable|string|max:50',

            // File surat tugas
            'file_surat_tugas' => 'nullable|file|mimes:pdf,doc,docx|max:10000',
        ];
    }

    public function messages(): array
    {
        return [
            'tahun.required' => 'Tahun wajib diisi',
            'sasaran.required' => 'Sasaran pemeriksaan wajib diisi',
            'jenis_pengawasan_id.required' => 'Jenis pengawasan wajib dipilih',
            'jenis_pengawasan_id.exists' => 'Jenis pengawasan tidak valid',
            'jabatans.required' => 'Detail jabatan minimal harus ada satu',
            'auditis.required' => 'Minimal harus ada satu auditi yang dipilih',
            'auditis.*.exists' => 'Auditi tidak valid',
            'irbanwil_id.exists' => 'Irbanwil tidak valid',
        ];
    }
}
