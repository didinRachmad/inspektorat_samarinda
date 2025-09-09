<?php

namespace App\Http\Requests\Auditi;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuditiRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'nama_auditi' => 'required|string|max:255|unique:auditis,nama_auditi',
            'kode_auditi' => 'nullable|string|max:100',
            'alamat'      => 'nullable|string|max:255',
            'telepon'     => 'nullable|string|max:20',
        ];
    }
}
