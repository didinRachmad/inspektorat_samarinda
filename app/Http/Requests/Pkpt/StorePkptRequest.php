<?php

namespace App\Http\Requests\Pkpt;

use Illuminate\Foundation\Http\FormRequest;

class StorePkptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // bisa diatur pakai Gate/Policy kalau perlu
    }

    public function rules(): array
    {
        return [
            'tahun' => 'required|digits:4',
            'bulan' => 'nullable|integer|min:1|max:12',
            'no_pkpt' => 'nullable|integer|min:1',

            'auditi_id' => 'required|exists:auditis,id',
            'ruang_lingkup' => 'nullable|string|max:255',
            'sasaran' => 'required|string|max:255',
            'jenis_pengawasan' => 'required|in:REVIEW,AUDIT,PENGAWASAN,EVALUASI,MONITORING,PRA_REVIEW',

            'jadwal_rmp_bulan' => 'nullable|integer|between:1,12',
            'jadwal_rsp_bulan' => 'nullable|integer|between:1,12',
            'jadwal_rpl_bulan' => 'nullable|integer|between:1,12',
            'jadwal_hp_hari'   => 'nullable|integer|min:1',

            'irbanwil' => 'nullable|string|max:255',
            'auditor_ringkas' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:500',

            // validasi nested untuk detail jabatan
            'jabatans' => 'required|array|min:1',
            'jabatans.*.jabatan' => 'required|in:PJ,WPJ,PT,KT,AT',
            'jabatans.*.jumlah' => 'required|integer|min:1',
            'jabatans.*.anggaran' => 'required|integer|min:0',

            // validasi nested untuk auditor
            'auditors' => 'nullable|array',
            'auditors.*.user_id' => 'nullable|exists:users,id',
            'auditors.*.nama_manual' => 'nullable|string|max:255',
            'auditors.*.jabatan' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'tahun.required' => 'Tahun wajib diisi',
            'sasaran.required' => 'Sasaran pemeriksaan wajib diisi',
            'jenis_pengawasan.in' => 'Jenis pengawasan tidak valid',
            'jabatans.required' => 'Detail jabatan minimal harus ada satu',
        ];
    }
}
