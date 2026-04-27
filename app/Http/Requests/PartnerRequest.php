<?php

namespace App\Http\Requests;

class PartnerRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'contact_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ];

        if ($this->isMethod('post')) {
            $rules['type'] = 'required|in:seller,buyer,supplier';
            $rules['name'] = 'required|string|max:255';
        } else {
            $rules['type'] = 'sometimes|in:seller,buyer,supplier';
            $rules['name'] = 'sometimes|string|max:255';
        }

        return $rules;
    }
}
