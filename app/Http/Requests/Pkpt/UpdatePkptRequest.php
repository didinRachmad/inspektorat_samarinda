<?php

namespace App\Http\Requests\Pkpt;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePkptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tahun' => 'required|digits:4',
            'bulan' => 'nullable|integer|min:1|max:12',
            'no_pkpt' => 'required|string|max:255',

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

            'jabatans' => 'required|array|min:1',
            'jabatans.*.jabatan' => 'required|in:PJ,WPJ,PT,KT,AT',
            'jabatans.*.jumlah' => 'nullable|integer|min:0',
            'jabatans.*.anggaran' => 'nullable|integer|min:0',

            'auditors' => 'nullable|array',
            'auditors.*.user_id' => 'nullable|exists:users,id',
            'auditors.*.nama_manual' => 'nullable|string|max:255',
            'auditors.*.jabatan' => 'nullable|string|max:50',
        ];
    }
}
