<?php

namespace App\Http\Requests\Pkpt;

use Illuminate\Foundation\Http\FormRequest;

class StorePkptAuditorRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pkpt_id' => 'required|exists:pkpts,id',
            'user_id' => 'nullable|exists:users,id',
            'nama_manual' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:50',
        ];
    }
}
