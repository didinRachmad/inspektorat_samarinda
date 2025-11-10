<?php

namespace App\Http\Requests\Faq;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => [
                'required',
                'string',
                'max:255',
                Rule::unique('faqs', 'question')->ignore($this->route('faq')),
            ],
            'answer' => ['required', 'string'],
        ];
    }
}
