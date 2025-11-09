<?php

namespace App\Http\Requests\Kka;

use Illuminate\Foundation\Http\FormRequest;

class StoreKkaRequest extends FormRequest
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
            'lhp_id' => 'required|exists:lhps,id',
            'judul' => ['required', 'string', 'max:255'],
            'uraian_prosedur' => 'nullable|string|max:1000',
            'hasil' => 'nullable|string',
            'file_kka' => 'nullable|file|mimes:pdf,doc,docx|max:10000',
            'auditor_id' => 'nullable|exists:users,id',
        ];
    }
}
