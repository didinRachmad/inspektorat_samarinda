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
            'pkpt_id' => 'required|exists:pkpts,id',
            'nomor_temuan' => 'nullable|string|max:255',
            'tanggal_temuan' => 'nullable|date',
            'uraian_temuan' => 'nullable|string',
            'rekomendasi' => 'nullable|string',
            'file_temuan' => 'nullable|file|mimes:pdf,doc,docx|max:10000', // max 10MB
        ];
    }
}
