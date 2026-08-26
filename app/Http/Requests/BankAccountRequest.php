<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountRequest extends FormRequest
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
        $rules = [
            'country_code' => 'required|in:TR,MK,UA,XK',
            'recipient_name' => 'required|string|max:150',
            'bank_name' => 'required|string|max:150',
            'iban' => 'required|string|max:34',
            'swift_bic' => 'required|string|max:11',
        ];

        // Purpose of payment is required only for Ukraine (UA)
        if ($this->input('country_code') === 'UA') {
            $rules['purpose_of_payment'] = 'required|string|max:255';
        } else {
            $rules['purpose_of_payment'] = 'nullable|string|max:255';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'country_code.required' => 'Please select a country.',
            'country_code.in' => 'Invalid country selected. Please choose from Turkey, North Macedonia, Ukraine, or Kosovo.',
            'recipient_name.required' => 'Recipient name is required.',
            'recipient_name.max' => 'Recipient name cannot exceed 150 characters.',
            'bank_name.required' => 'Bank name is required.',
            'bank_name.max' => 'Bank name cannot exceed 150 characters.',
            'iban.required' => 'IBAN is required.',
            'iban.max' => 'IBAN cannot exceed 34 characters.',
            'swift_bic.required' => 'SWIFT/BIC code is required.',
            'swift_bic.max' => 'SWIFT/BIC code cannot exceed 11 characters.',
            'purpose_of_payment.required' => 'Purpose of payment is required for Ukraine.',
            'purpose_of_payment.max' => 'Purpose of payment cannot exceed 255 characters.',
        ];
    }
}
