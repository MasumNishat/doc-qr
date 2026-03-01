<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
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
            'verification_code' => ['required', 'string', 'size:10', 'unique:documents,verification_code,'.$this->route('document')->id],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'verification_code.required' => 'Verification code is required.',
            'verification_code.size' => 'Verification code must be exactly 10 characters.',
            'verification_code.unique' => 'This verification code is already in use.',
        ];
    }
}
