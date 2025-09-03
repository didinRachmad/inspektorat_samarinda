<?php

namespace App\Http\Requests\Pkpt;

use Illuminate\Foundation\Http\FormRequest;

class StorePkptJabatanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pkpt_id' => 'required|exists:pkpts,id',
            'jabatan' => 'required|in:PJ,WPJ,PT,KT,AT',
            'jumlah' => 'required|integer|min:1',
            'anggaran' => 'required|integer|min:0',
        ];
    }
}
