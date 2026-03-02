<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
            'document' => ['required', 'file', 'mimes:pdf,jpeg,jpg,png,gif', 'max:10240'],
            'verification_code' => ['nullable', 'string', 'size:10', 'unique:documents,verification_code'],
            'crts_no' => ['required', 'string', 'max:100'],
            'date' => ['required', 'date'],
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
            'document.required' => 'Please select a document to upload.',
            'document.mimes' => 'Document must be a PDF or image file (JPEG, JPG, PNG, GIF).',
            'document.max' => 'Document size must not exceed 10MB.',
            'verification_code.size' => 'Verification code must be exactly 10 characters.',
            'verification_code.unique' => 'This verification code is already in use.',
            'date.required' => 'Date is required.',
            'date.date' => 'Date should be valid date format.',
            'crts_no.required' => 'CRTS No. is required.',
            'crts_no.max' => 'CRTS No. must not exceed 100 characters.',
        ];
    }
}
