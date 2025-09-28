<?php

namespace App\Http\Requests\Lha;

use Illuminate\Foundation\Http\FormRequest;

class StoreLhaRequest extends FormRequest
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
            'nomor_lha' => 'nullable|string|max:255',
            'tanggal_lha' => ['required', 'date', 'after_or_equal:today'],
            'uraian_temuan' => 'nullable|string',
            'rekomendasi' => 'nullable|string',
            'file_lha' => 'nullable|file|mimes:pdf,doc,docx|max:10000', // max 10MB
        ];
    }
}
