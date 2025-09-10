<?php

namespace App\Http\Requests\KodeTemuan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKodeTemuanRequest extends FormRequest
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
            'kode' => [
                'required',
                'string',
                'max:255',
                // Unique, kecuali saat update
                Rule::unique('kode_temuans', 'kode')->ignore($this->route('kode_temuan')),
            ],
            'nama_temuan' => 'required|string|max:255',
            'parent_id'   => 'nullable|exists:kode_temuans,id',
            'level'       => 'required|integer|min:1',
            'urutan'      => 'required|integer|min:0',
        ];
    }
}
