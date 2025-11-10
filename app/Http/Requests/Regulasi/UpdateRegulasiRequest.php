<?php

namespace App\Http\Requests\Regulasi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegulasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('regulasis', 'title')->ignore($this->route('regulasi')),
            ],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'], // file opsional saat update
        ];
    }
}
