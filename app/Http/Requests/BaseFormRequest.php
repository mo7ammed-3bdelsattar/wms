<?php

namespace App\Http\Requests;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class BaseFormRequest extends FormRequest
{
    use ApiResponse;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {

        if ($this->is('api/*')) {
            $response = $this->validationErrorResponse($validator->errors(),);
            throw new ValidationException($validator, $response);
        }

        $response = redirect()
            ->back()
            ->withInput($this->all())
            ->withErrors($validator);

        throw new ValidationException($validator, $response);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => __('validation/messages.required'),
            'string' => __('validation/messages.string'),
            'max' => __('validation/messages.max'),
            'min' => __('validation/messages.min'),
            'email' => __('validation/messages.email'),
            'unique' => __('validation/messages.unique'),
            'numeric' => __('validation/messages.numeric'),
            'integer' => __('validation/messages.integer'),
            'date' => __('validation/messages.date'),
            'after' => __('validation/messages.after'),
            'before' => __('validation/messages.before'),
            'after_or_equal' => __('validation/messages.after_or_equal'),
            'before_or_equal' => __('validation/messages.before_or_equal'),
            'confirmed' => __('validation/messages.confirmed'),
            'exists' => __('validation/messages.exists'),
            'in' => __('validation/messages.in'),
            'not_in' => __('validation/messages.not_in'),
            'regex' => __('validation/messages.regex'),
            'size' => __('validation/messages.size'),
            'between' => __('validation/messages.between'),
            'mimes' => __('validation/messages.mimes'),
            'mimetypes' => __('validation/messages.mimetypes'),
        ];
    }
}
